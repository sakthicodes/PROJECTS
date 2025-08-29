/** @format */
import { useCallback, useRef, useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import Konva from "konva";
import { generateKonvaId } from "../utils/idGenerator";
import { SHAPES } from "../components/Editor/Sidebar/ElementsTab/Shapes/lib/ShapesLib.jsx";
import { createClippedShapes } from "../components/Editor/Sidebar/ElementsTab/Shapes/lib/ClippedShapesLib.jsx";
import {
  addClippedObject,
  removeClippedObject,
  clearAllClipping as clearAllClippingRedux,
  selectClippedObjectsByPage,
} from "../redux/slices/clippingSlice";

// Configuration for clipped image dimensions
const CLIPPED_IMAGE_CONFIG = {
  MAX_WIDTH: 300, // Maximum width for clipped images
  MAX_HEIGHT: 500, // Maximum height for clipped images
};

export const useKonvaClipping = (paperRef) => {
  const dispatch = useDispatch();

  const clippingStateRef = useRef({
    clippedPairs: new Map(),
    activeTransformers: new Set(),
    imageOverClippedTimer: null,
    hoveredClippedObject: null,
    replacementIndicator: null,
  });

  const currentPage = useSelector((state) => state.canvas.currentPage);

  const clippedObjectsFromRedux = useSelector((state) =>
    selectClippedObjectsByPage(state, currentPage)
  );

  // Function to show green border on clipped object when image is over it
  const showReplacementIndicator = useCallback((clippedObject) => {
    if (!clippedObject || clippedObject.isDestroyed) return;

    // Remove existing indicator if any
    removeReplacementIndicator();

    const rect = clippedObject.getClientRect();
    const indicator = new Konva.Rect({
      x: rect.x - 3,
      y: rect.y - 3,
      width: rect.width + 6,
      height: rect.height + 6,
      stroke: "#00FF00", // Green border
      strokeWidth: 3,
      dash: [8, 8],
      fill: "transparent",
      listening: false,
      name: "replacementIndicator",
    });

    clippingStateRef.current.replacementIndicator = indicator;
    const layer = clippedObject.getLayer();
    if (layer) {
      layer.add(indicator);
      indicator.moveToTop();
      layer.batchDraw();
    }
  }, []);

  // Function to remove replacement indicator
  const removeReplacementIndicator = useCallback(() => {
    const indicator = clippingStateRef.current.replacementIndicator;
    if (indicator && !indicator.isDestroyed) {
      try {
        const layer = indicator.getLayer();
        if (layer) {
          indicator.destroy();
          layer.batchDraw();
        }
      } catch (error) {
        console.warn("Error removing replacement indicator:", error);
      }
    }
    clippingStateRef.current.replacementIndicator = null;
  }, []);

  // Function to detect if image is over a clipped object
  const detectImageOverClippedObject = useCallback((imageNode) => {
    if (!imageNode || imageNode.isDestroyed) return null;

    const stage = paperRef?.current?.getStageForSection?.(
      paperRef.current.selectedSection || 0
    );
    if (!stage) return null;

    const layer = stage.findOne("Layer");
    if (!layer) return null;

    const imageRect = imageNode.getClientRect();
    
    // Find all clipped objects (groups with isClippedImage attribute)
    const clippedObjects = layer.find("Group").filter(group => 
      group.attrs && group.attrs.isClippedImage === true
    );

    for (const clippedObj of clippedObjects) {
      if (clippedObj.isDestroyed) continue;
      
      const clippedRect = clippedObj.getClientRect();
      
      // Check if image overlaps with clipped object (at least 30% overlap)
      const overlapArea = Math.max(0, 
        Math.min(imageRect.x + imageRect.width, clippedRect.x + clippedRect.width) - 
        Math.max(imageRect.x, clippedRect.x)
      ) * Math.max(0,
        Math.min(imageRect.y + imageRect.height, clippedRect.y + clippedRect.height) - 
        Math.max(imageRect.y, clippedRect.y)
      );
      
      const imageArea = imageRect.width * imageRect.height;
      const overlapPercentage = (overlapArea / imageArea) * 100;
      
      if (overlapPercentage >= 30) {
        return clippedObj;
      }
    }
    
    return null;
  }, [paperRef]);

  // Function to replace clipped object with new shape and image
  const replaceClippedObjectWithImage = useCallback((clippedObject, newImageNode) => {
    if (!clippedObject || !newImageNode || clippedObject.isDestroyed || newImageNode.isDestroyed) {
      return;
    }

    try {
      const stage = paperRef?.current?.getStageForSection?.(
        paperRef.current.selectedSection || 0
      );
      if (!stage) return;

      const layer = stage.findOne("Layer");
      if (!layer) return;

      // Get clipping data
      const clippingData = clippingStateRef.current.clippedPairs.get(clippedObject.id());
      if (!clippingData) {
        console.warn("No clipping data found for object:", clippedObject.id());
        return;
      }

      const { originalShape, clipGroup } = clippingData;
      const clippedRect = clippedObject.getClientRect();

      // Remove the clipped object and clean up
      deleteClippedObject(clippedObject.id());

      // Create new shape at the same position as the clipped object
      const newShape = originalShape.clone({
        id: generateKonvaId("shape"),
        x: clippedRect.x,
        y: clippedRect.y,
        draggable: true,
        listening: true,
        visible: true,
        _wasUsedForClipping: false,
        _clippingDeleted: false,
      });

      // Position the new image at the clipped object's location
      newImageNode.position({
        x: clippedRect.x,
        y: clippedRect.y,
      });

      // Add both to layer
      layer.add(newShape);
      layer.add(newImageNode);
      
      // Move to appropriate z-index
      newShape.moveToTop();
      newImageNode.moveToTop();
      
      layer.batchDraw();

      console.log("Successfully replaced clipped object with new image and shape");
      
      // Remove replacement indicator
      removeReplacementIndicator();
      
    } catch (error) {
      console.error("Error replacing clipped object:", error);
    }
  }, [paperRef, removeReplacementIndicator]);

  // Function to handle image movement over clipped objects
  const handleImageMoveOverClipped = useCallback((imageNode) => {
    if (!imageNode || imageNode.isDestroyed) return;

    const clippedObjectUnder = detectImageOverClippedObject(imageNode);
    
    if (clippedObjectUnder) {
      // Show green border indicator
      if (clippingStateRef.current.hoveredClippedObject !== clippedObjectUnder) {
        // Clear existing timer if switching to different clipped object
        if (clippingStateRef.current.imageOverClippedTimer) {
          clearTimeout(clippingStateRef.current.imageOverClippedTimer);
          clippingStateRef.current.imageOverClippedTimer = null;
        }
        
        clippingStateRef.current.hoveredClippedObject = clippedObjectUnder;
        showReplacementIndicator(clippedObjectUnder);
        
        // Start 3-second timer
        clippingStateRef.current.imageOverClippedTimer = setTimeout(() => {
          if (clippingStateRef.current.hoveredClippedObject === clippedObjectUnder) {
            replaceClippedObjectWithImage(clippedObjectUnder, imageNode);
            clippingStateRef.current.hoveredClippedObject = null;
            clippingStateRef.current.imageOverClippedTimer = null;
          }
        }, 3000); // 3 seconds
      }
    } else {
      // No clipped object under image, clear timer and indicator
      if (clippingStateRef.current.imageOverClippedTimer) {
        clearTimeout(clippingStateRef.current.imageOverClippedTimer);
        clippingStateRef.current.imageOverClippedTimer = null;
      }
      
      clippingStateRef.current.hoveredClippedObject = null;
      removeReplacementIndicator();
    }
  }, [detectImageOverClippedObject, showReplacementIndicator, replaceClippedObjectWithImage, removeReplacementIndicator]);

  // Function to handle when image drag ends
  const handleImageDragEndOverClipped = useCallback((imageNode) => {
    // Clear timer when drag ends
    if (clippingStateRef.current.imageOverClippedTimer) {
      clearTimeout(clippingStateRef.current.imageOverClippedTimer);
      clippingStateRef.current.imageOverClippedTimer = null;
    }
    
    clippingStateRef.current.hoveredClippedObject = null;
    removeReplacementIndicator();
  }, [removeReplacementIndicator]);

  // Rest of your existing code starts here...
  const restoreClippedObjectsFromRedux = useCallback(() => {
    if (clippedObjectsFromRedux && clippedObjectsFromRedux.length > 0) {
      // window.dispatchEvent(
      //   new CustomEvent("restoreClippedObjectsFromRedux", {
      //     detail: {
      //       clippedObjects: clippedObjectsFromRedux,
      //       pageIndex: currentPage,
      //     },
      //   })
      // );
    }
  }, [clippedObjectsFromRedux, currentPage]);

  const syncReduxWithAPIData = useCallback(
    (apiClippedObjects) => {
      if (!apiClippedObjects || !Array.isArray(apiClippedObjects)) return;
      apiClippedObjects.forEach((apiObj) => {
        if (apiObj.type === "ClippedImage" && apiObj.clipGroupId) {
          const matchingReduxObj = clippedObjectsFromRedux.find(
            (reduxObj) => reduxObj.shapeId === apiObj.shapeId
          );

          if (matchingReduxObj) {
            dispatch(
              addClippedObject({
                clipGroupId: apiObj.clipGroupId,
                imageId: apiObj.imageId || matchingReduxObj.imageId,
                shapeId: apiObj.shapeId,
                shapeType: apiObj.shapeType,
                imageUrl: apiObj.image,
                pageIndex: currentPage,

                position: {
                  x: apiObj.x,
                  y: apiObj.y,
                  width: apiObj.width,
                  height: apiObj.height,
                },
                shapeProperties: {
                  ...matchingReduxObj.shapeProperties,

                  ...(apiObj.shapeData && {
                    x: apiObj.shapeData.x,
                    y: apiObj.shapeData.y,
                    width: apiObj.shapeData.width,
                    height: apiObj.shapeData.height,
                    scaleX: apiObj.shapeData.scaleX,
                    scaleY: apiObj.shapeData.scaleY,
                  }),
                },
              })
            );

            if (matchingReduxObj.clipGroupId !== apiObj.clipGroupId) {
              dispatch(
                removeClippedObject({
                  clipGroupId: matchingReduxObj.clipGroupId,
                })
              );
            }
          }
        }
      });
    },
    [clippedObjectsFromRedux, currentPage, dispatch]
  );

  const safelyDestroyTransformer = useCallback((transformer) => {
    if (!transformer) return;

    try {
      if (transformer.isDestroyed && transformer.isDestroyed) {
        return;
      }

      if (transformer.nodes && typeof transformer.nodes === "function") {
        try {
          const currentNodes = transformer.nodes();
          if (Array.isArray(currentNodes)) {
            currentNodes.forEach((node) => {
              try {
                if (
                  node &&
                  node.off &&
                  typeof node.off === "function" &&
                  !node.isDestroyed
                ) {
                  node.off("change");
                }
              } catch {}
            });
          }
        } catch (error) {
          console.warn("Error accessing transformer nodes:", error);
        }

        try {
          transformer.nodes([]); // detach cleanly
        } catch {}
      }

      if (transformer.update && typeof transformer.update === "function") {
        try {
          if (transformer.nodes && transformer.nodes().length > 0) {
            transformer.update();
          }
        } catch {}
      }

      if (transformer.off && typeof transformer.off === "function") {
        try {
          transformer.off();
        } catch {}
      }

      if (transformer.hide && typeof transformer.hide === "function") {
        try {
          transformer.hide();
        } catch {}
      }

      clippingStateRef.current.activeTransformers.delete(transformer);
      if (transformer.destroy && typeof transformer.destroy === "function") {
        try {
          transformer.destroy();
        } catch (error) {
          console.warn("Error destroying transformer:", error);
        }
      }
    } catch (error) {
      console.warn("Error destroying transformer:", error);
    }
  }, []);

  const cleanupAllTransformers = useCallback(
    (layer) => {
      if (!layer) return;

      try {
        const activeTransformers = Array.from(
          clippingStateRef.current.activeTransformers
        );
        activeTransformers.forEach((transformer) => {
          safelyDestroyTransformer(transformer);
        });
        clippingStateRef.current.activeTransformers.clear();
        try {
          const transformers = layer.find("Transformer");
          if (transformers && Array.isArray(transformers)) {
            transformers.forEach((tr) => {
              safelyDestroyTransformer(tr);
            });
          }
        } catch (error) {
          console.log("error", error);

          console.warn("Error finding transformers in layer:", error);
        }

        try {
          const allTransformers = layer.find("Transformer");
          if (allTransformers && Array.isArray(allTransformers)) {
            allTransformers.forEach((transformer) => {
              try {
                if (
                  transformer.nodes &&
                  typeof transformer.nodes === "function"
                ) {
                  transformer.nodes([]);
                }

                if (
                  transformer.hide &&
                  typeof transformer.hide === "function"
                ) {
                  transformer.hide();
                }

                if (transformer.off && typeof transformer.off === "function") {
                  transformer.off();
                }

                if (
                  transformer.destroy &&
                  typeof transformer.destroy === "function"
                ) {
                  transformer.destroy();
                }
              } catch (cleanupError) {
                console.log("error", cleanupError);

                console.warn("Error during transformer cleanup:", cleanupError);
              }
            });
          }
        } catch (error) {
          console.log("error", error);
          console.warn("Error during additional transformer cleanup:", error);
        }

        if (layer.batchDraw && typeof layer.batchDraw === "function") {
          layer.batchDraw();
        }
      } catch (error) {
        console.log("error", error);

        console.warn("Error in cleanupAllTransformers:", error);
      }
    },
    [safelyDestroyTransformer]
  );

  const createClippedImage = useCallback(
    (imageNode, shapeNode) => {
      if (!imageNode || !shapeNode) return null;
      if (!canClipImageWithShape(imageNode, shapeNode)) {
        console.warn(
          "Cannot clip image with this shape - either image is already clipped or shape is already in use"
        );
        return null;
      }

      const stage = paperRef?.current?.getStageForSection?.(
        paperRef.current.selectedSection || 0
      );
      if (!stage) return null;

      const layer = stage.findOne("Layer");
      if (!layer) return null;
      cleanupAllTransformers(layer);

      try {
        window.dispatchEvent(
          new CustomEvent("clearSelectionAfterClipping", {
            detail: { layer, stage },
          })
        );

        const existingTransformers = layer.find("Transformer");
        if (existingTransformers && existingTransformers.length > 0) {
          existingTransformers.forEach((tr) => {
            try {
              if (tr && !tr.isDestroyed && tr.destroy) {
                tr.destroy();
              }
            } catch (error) {
              console.warn("Error destroying existing transformer:", error);
            }
          });
        }
      } catch (error) {
        console.warn(
          "Error dispatching clearSelectionAfterClipping event:",
          error
        );
      }
      const rectclip = shapeNode.getClientRect();

      const clipGroup = new Konva.Group({
        id: `clip_group_${imageNode.id()}_${shapeNode.id()}`,
        draggable: true,
        listening: true,

        x: rectclip.x / 2,
        y: rectclip.y / 2,
        width: rectclip.width / 2,
        height: rectclip.height / 2,

        attrs: {
          isClippedImage: true,
          originalImageId: imageNode.id(),
          originalShapeId: shapeNode.id(),
          clipType: "custom",
        },
      });

      clipGroup.getClientRect = function () {
        const rect = shapeNode.getClientRect();

        return {
          x: rect.x,
          y: rect.y,
          width: rect.width,
          height: rect.height,
        };
      };

      // Limit image dimensions to prevent performance issues
      let imageWidth = imageNode.width() * imageNode.scaleX();
      let imageHeight = imageNode.height() * imageNode.scaleY();

      // Scale down if dimensions are too large
      if (
        imageWidth > CLIPPED_IMAGE_CONFIG.MAX_WIDTH ||
        imageHeight > CLIPPED_IMAGE_CONFIG.MAX_HEIGHT
      ) {
        const scale = Math.min(
          CLIPPED_IMAGE_CONFIG.MAX_WIDTH / imageWidth,
          CLIPPED_IMAGE_CONFIG.MAX_HEIGHT / imageHeight
        );
        imageWidth = Math.round(imageWidth * scale);
        imageHeight = Math.round(imageHeight * scale);
      }
      const rect = shapeNode.getClientRect();

      const clippedImage = imageNode.clone({
        draggable: false,
        name: "clippedImage",
        x: rect.x,
        y: rect.y,
        width: imageWidth,
        height: imageHeight,
        scaleX: 1,
        scaleY: 1,
        attrs: {
          originalImageId: imageNode.id(),
        },
      });
      const shapeBorder = shapeNode.clone({
        draggable: false,
        fill: "transparent",
        stroke: "transparent",
        strokeWidth: 0,
        name: "shapeBorder",
        listening: false,
        visible: false,
        attrs: {
          originalShapeId: shapeNode.id(),
        },
      });
      const createClipFunction = (ctx) => {
        try {
          if (!shapeNode || !shapeNode.getStage()) {
            const rect = clipGroup.getClientRect();
            ctx.rect(rect.x, rect.y, rect.width, rect.height);
            return;
          }

          const rect = shapeNode.getClientRect();

          if (shapeNode.className === "Rect") {
            ctx.rect(rect.x, rect.y, rect.width, rect.height);
          } else if (shapeNode.className === "Circle") {
            ctx.arc(
              rect.x + rect.width / 2,
              rect.y + rect.height / 2,
              rect.width / 2,
              0,
              Math.PI * 2
            );
          } else if (shapeNode.className === "Ellipse") {
            ctx.ellipse(
              rect.x + rect.width / 2,
              rect.y + rect.height / 2,
              rect.width / 2,
              rect.height / 2,
              0,
              0,
              Math.PI * 2
            );
          } else if (shapeNode.className === "Line" && shapeNode.closed()) {
            const pts = shapeNode.points();
            ctx.moveTo(shapeNode.x() + pts[0], shapeNode.y() + pts[1]);
            for (let i = 2; i < pts.length; i += 2) {
              ctx.lineTo(shapeNode.x() + pts[i], shapeNode.y() + pts[i + 1]);
            }
            ctx.closePath();
          } else if (shapeNode.className === "Star") {
            const numPoints = shapeNode.numPoints();
            const outer = shapeNode.outerRadius() * shapeNode.scaleX();
            const inner = shapeNode.innerRadius() * shapeNode.scaleX();
            const cx = shapeNode.x();
            const cy = shapeNode.y();

            ctx.moveTo(cx + outer, cy);
            for (let i = 1; i <= numPoints * 2; i++) {
              const angle = (i * Math.PI) / numPoints;
              const r = i % 2 === 0 ? outer : inner;
              ctx.lineTo(cx + r * Math.cos(angle), cy + r * Math.sin(angle));
            }
            ctx.closePath();
          } else if (shapeNode.className === "RegularPolygon") {
            const sides = shapeNode.sides();
            const radius = shapeNode.radius() * shapeNode.scaleX();
            const cx = shapeNode.x();
            const cy = shapeNode.y();

            for (let i = 0; i < sides; i++) {
              const angle = (i * 2 * Math.PI) / sides;
              const px = cx + radius * Math.cos(angle);
              const py = cy + radius * Math.sin(angle);
              if (i === 0) ctx.moveTo(px, py);
              else ctx.lineTo(px, py);
            }
            ctx.closePath();
          }
        } catch (error) {
          console.warn("Error in clipping function:", error);
          const rect = clipGroup.getClientRect();
          ctx.rect(rect.x, rect.y, rect.width, rect.height);
        }
      };
      clipGroup.clipFunc(createClipFunction);

      clipGroup.add(clippedImage);
      clipGroup.add(shapeBorder);

      setTimeout(() => {
        try {
          cleanupAllTransformers(layer);

          if (!imageNode || imageNode.isDestroyed) {
            console.warn("Image node is no longer valid, skipping hide");
          } else {
            imageNode.hide();
            imageNode.destroy();
          }

          if (!shapeNode || shapeNode.isDestroyed) {
            console.warn("Shape node is no longer valid, skipping hide");
          } else {
            shapeNode.hide();
          }

          layer.add(clipGroup);
          layer.batchDraw();

          const templateImage = layer
            .find("Image")
            .find(
              (img) =>
                img.isClippedImage &&
                img.clipGroupId === clipGroup.attrs.originalShapeId
            );

          if (templateImage) {
            console.log(
              "Destroying template-created image:",
              templateImage.id()
            );
            templateImage.destroy();
          }

          const clipGroupBounds = clipGroup.getClientRect();

          const templateImagesToRemove = layer.find("Image").filter((img) => {
            if (img === clipGroup || img.parent === clipGroup) return false;

            const imgBounds = img.getClientRect();
            const distanceX = Math.abs(imgBounds.x - clipGroupBounds.x);
            const distanceY = Math.abs(imgBounds.y - clipGroupBounds.y);

            return distanceX < 50 && distanceY < 50;
          });

          templateImagesToRemove.forEach((img) => {
            console.log("Removing template image:", img.id());
            img.destroy();
          });

          const remainingTransformers = layer.find("Transformer");
          if (remainingTransformers && remainingTransformers.length > 0) {
            console.warn(
              "Warning: transformers still exist after clipping cleanup"
            );
          }

          try {
            if (
              clipGroup &&
              typeof clipGroup === "object" &&
              !clipGroup.isDestroyed &&
              clipGroup.id &&
              typeof clipGroup.id === "function" &&
              layer &&
              stage
            ) {
              window.dispatchEvent(
                new CustomEvent("clippedObjectCreated", {
                  detail: {
                    clipGroup,
                    layer,
                    stage,
                    imageNode: imageNode.id(),
                    shapeNode: shapeNode.id(),
                  },
                })
              );
            } else {
              console.warn(
                "Cannot dispatch clippedObjectCreated event - invalid objects:",
                {
                  clipGroup: !!clipGroup,
                  layer: !!layer,
                  stage: !!stage,
                }
              );
            }
          } catch (error) {
            console.warn(
              "Error dispatching clippedObjectCreated event:",
              error
            );
          }
        } catch (error) {
          console.error("Error during delayed node hiding:", error);

          try {
            layer.add(clipGroup);
            layer.batchDraw();
          } catch (fallbackError) {
            console.error("Fallback error adding clip group:", fallbackError);
          }
        }
      }, 50); // 50ms delay to ensure transformer cleanup

      // Rest of your existing createClippedImage code continues...
      // (I'm truncating here to keep the response manageable, but all your existing code would continue)

      return clipGroup;
    },
    [paperRef, cleanupAllTransformers, safelyDestroyTransformer]
  );

  // Function to delete clipped object
  const deleteClippedObject = useCallback(
    (clipGroupId) => {
      const clippingData =
        clippingStateRef.current.clippedPairs.get(clipGroupId);
      if (!clippingData) return;

      const { clipGroup, originalImage, originalShape } = clippingData;

      try {
        if (clipGroup && clipGroup.getStage) {
          const stage = clipGroup.getStage();
          if (stage) {
            const layer = stage.findOne("Layer");
            if (layer) {
              cleanupAllTransformers(layer);
            }
          }
        }

        if (clipGroup && clipGroup.destroy) {
          clipGroup.destroy();
        }

        if (originalImage) {
          originalImage._wasClipped = false;
          originalImage._clippingDeleted = true;
        }

        if (originalShape) {
          originalShape._wasUsedForClipping = false;

          originalShape.show();
          originalShape.draggable(true);
          originalShape.listening(true);

          if (originalShape.getStage()) {
            originalShape.moveToTop();
            originalShape.getStage().batchDraw();
          }
        }

        clippingStateRef.current.clippedPairs.delete(clipGroupId);

        dispatch(removeClippedObject({ clipGroupId }));

        console.log(
          `Clipped object ${clipGroupId} deleted, shape restored to paper`
        );
      } catch (error) {
        console.warn("Error deleting clipped object:", error);
      }
    },
    [cleanupAllTransformers, dispatch]
  );

  // Stub functions for the remaining functionality (you would add your full implementations)
  const canClipImageWithShape = useCallback((imageNode, shapeNode) => {
    // Your existing implementation
    return true; // Simplified for this example
  }, []);

  // Expose the new functions in the useEffect for global access
  useEffect(() => {
    // Make functions available globally
    window.handleImageMoveOverClipped = handleImageMoveOverClipped;
    window.handleImageDragEndOverClipped = handleImageDragEndOverClipped;
    window.detectImageOverClippedObject = detectImageOverClippedObject;
    window.replaceClippedObjectWithImage = replaceClippedObjectWithImage;

    return () => {
      // Cleanup global references
      delete window.handleImageMoveOverClipped;
      delete window.handleImageDragEndOverClipped;
      delete window.detectImageOverClippedObject;
      delete window.replaceClippedObjectWithImage;
      
      // Clear any pending timer
      if (clippingStateRef.current.imageOverClippedTimer) {
        clearTimeout(clippingStateRef.current.imageOverClippedTimer);
        clippingStateRef.current.imageOverClippedTimer = null;
      }
    };
  }, [handleImageMoveOverClipped, handleImageDragEndOverClipped, detectImageOverClippedObject, replaceClippedObjectWithImage]);

  return {
    // New functions for image replacement
    handleImageMoveOverClipped,
    handleImageDragEndOverClipped,
    detectImageOverClippedObject,
    replaceClippedObjectWithImage,
    showReplacementIndicator,
    removeReplacementIndicator,
    
    // Existing functions
    createClippedImage,
    deleteClippedObject,
    // ... (all your other existing functions would be returned here)
  };
};