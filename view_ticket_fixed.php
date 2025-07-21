<?php require_once "src/layout/head.php"; ?>

<body>
   <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
         <div id="overlay" class="overlay">
            <span class="loader"></span>
         </div>
         <?php require_once "src/layout/sidebar.php"; ?>
         <div class="layout-page">
            <?php require_once "src/layout/navbar.php"; ?>
            <div class="content-wrapper">
               <div class="container-xxl flex-grow-1 container-p-y">
                  <div class="app-email card">
                     <div class="row g-0">
                        <!-- Email Sidebar -->
                        <div class="col app-email-sidebar border-end flex-grow-0" id="app-email-sidebar">
                           <div class="btn-compost-wrapper d-grid ">
                              <button class="btn btn-primary btn-compose" onclick="goBackToTickets()">
                                 <i class="icon-base bx bx-arrow-back me-2"></i>Back to Tickets
                              </button>
                           </div>
                           <!-- Ticket Info Sidebar -->
                           <div class="email-filters pt-4 pb-2 ps">
                              <div class="ticket-info-sidebar">
                                 <h6 class="mb-2 text-uppercase text-body-secondary">Ticket Details</h6>
                                 <div class="mb-2 d-flex gap-2">
                                    <div class="d-block">
                                    <label class="text-body-secondary small">Status</label>
                                    <div class="mt-1">
                                       <span class="badge bg-label-success" id="ticketStatus">Open</span>
                                    </div>
                                    </div>
                                     <div class="d-block">
                                     <label class="text-body-secondary small">Priority</label>
                                    <div class="mt-1">
                                       <span class="badge bg-label-warning" id="ticketPriority">High</span>
                                    </div>
                                    </div>
                                 </div>
                                  
                                 <div class="mb-2">
                                    <label class="text-body-secondary small">Type</label>
                                    <div class="mt-1">
                                       <span class="text-heading" id="ticketType">Incident</span>
                                    </div>
                                 </div>
                                 <div class="mb-2">
                                    <label class="text-body-secondary small">Assignee</label>
                                    <div class="mt-1 d-flex align-items-center">
                                       <span class="avatar avatar-xs me-2">
                                          <span class="avatar-initial rounded-circle bg-label-primary" id="assigneeAvatar">NA</span>
                                       </span>
                                       <span class="text-heading" id="assigneeName">Not Assigned</span>
                                    </div>
                                 </div>
                                 <div class="mb-2">
                                    <label class="text-body-secondary small">Tags</label>
                                    <div class="mt-1" id="ticketTags">
                                       <span class="badge bg-label-info me-1">Bug</span>
                                       <span class="badge bg-label-info me-1">Critical</span>
                                    </div>
                                 </div>
                                 <div class="mb-2">
                                    <label class="text-body-secondary small">CCs</label>
                                    <div class="mt-1" id="ticketCCs">
                                       <small class="text-body">john@example.com</small>
                                    </div>
                                 </div>
                                 <div class="mb-2">
                                    <label class="text-body-secondary small">Ticket ID</label>
                                    <div class="mt-1">
                                       <span class="text-heading" id="ticketNumber"> </span>
                                    </div>
                                 </div>
                                 <div class="mb-2">
                                    <label class="text-body-secondary small">Dev Ticket ID</label>
                                    <div class="mt-1">
                                       <span class="text-heading" id="devTicketNumber"> </span>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!--/ Email Sidebar -->

                        <!-- Emails List -->
                        <div class="col app-emails-list border-end">
                           <div class="card shadow-none border-0 rounded-0" >
                              <div class="card-body emails-list-header p-3 py-2">
                                 <!-- Email List: Search -->
                                 <div class="d-flex justify-content-between align-items-center px-3 mt-2">
                                    <div class="d-flex align-items-center w-100">
                                       <i class="icon-base bx bx-menu icon-lg cursor-pointer d-block d-lg-none me-4 mb-4" data-bs-toggle="sidebar" data-target="#app-email-sidebar" data-overlay=""></i>
                                       <div class="mb-4 w-100">
                                          <h5 class="mb-0" id="ticketSubject"></h5>
                                          <small class="text-body-secondary" id="ticketRequester"> </small>
                                       </div>
                                    </div>
                                 </div>
                                 <hr class="mx-n3 emails-list-header-hr mb-2">
                                 <!-- Email List: Actions -->
                                  
                              </div>
                              
                              <div class=""id="app-email-view">
                                <div class="app-email-view-content py-4 ps-3 ps--active-y"  >
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary mb-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-body-secondary">Loading email conversation...</p>
                                    </div>
                                </div>
                                </div>
                           </div>
                        </div>
                        <!-- /Emails List -->

                        <!-- Email View -->
                        <div class="col app-email-view flex-grow-0 bg-lighter show">
                           <div class="card shadow-none border-0 rounded-0 app-email-view-header p-5 pt-md-4 py-2">
                              <!-- Email View : Title  bar-->
                              <div class="d-flex justify-content-between align-items-center">
                                 <div class="d-flex align-items-center overflow-hidden">
                                    <h6 class="text-truncate mb-0 me-2 fw-normal">Requester Information</h6>
                                 </div>
                              </div>
                              <hr class="app-email-view-hr mx-n5 mb-2">
                              <!-- Sender Info Panel -->
                              <div class="sender-info-content">
                                 <div class="mb-2 d-flex align-items-center">
                                    <span class="avatar avatar-md me-3">
                                       <span class="avatar-initial rounded-circle bg-label-info" id="senderAvatar">NA</span>
                                    </span>
                                    <div>
                                       <h6 class="mb-0" id="senderName">Loading...</h6>
                                       <small class="text-body-secondary" id="senderEmail">Loading...</small>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-12">
                                       <small class="text-body-secondary">Email</small>
                                       <div class="text-heading" id="requesterEmail">Loading...</div>
                                    </div>
                                 </div>
                                 <div class="row mt-3">
                                    <div class="col-6">
                                       <small class="text-body-secondary">Created</small>
                                       <div class="text-heading" id="ticketCreatedDate">Loading...</div>
                                    </div>
                                    <div class="col-6">
                                       <small class="text-body-secondary">Updated</small>
                                       <div class="text-heading" id="ticketUpdatedDate">Loading...</div>
                                    </div>
                                 </div>
                                 
                                 <!-- Ticket Actions Section -->
                                 <hr class="my-4">
                                 <h6 class="mb-2 text-uppercase text-body-secondary">Quick Actions</h6>
                                 
                                 <!-- Status Change -->
                                 <div class="mb-2">
                                    <label class="text-body-secondary small">Change Status</label>
                                    <div class="d-flex align-items-center mt-1">
                                       <select class="form-select form-select-sm me-2" id="statusChangeSelect">
                                          <option value="Open">Open</option>
                                          <option value="Pending">Pending</option>
                                          <option value="On Hold">On Hold</option>
                                          <option value="Solved">Solved</option>
                                       </select>
                                       <button class="btn btn-sm btn-primary" onclick="updateTicketStatus()">Apply</button>
                                    </div>
                                 </div>
                                 
                                 <!-- Assignee Edit -->
                                 <div class="mb-2">
                                    <label class="text-body-secondary small">Assignee</label>
                                    <div class="mt-1">
                                       <input id="TagifyUserList" name="assignee" class="form-control" value="" placeholder="Select assignee...">
                                    </div>
                                    <small class="text-muted">Current: <span id="currentAssigneeName">Not Assigned</span></small>
                                 </div>
                                 
                                 <!-- Tags Edit -->
                                 <div class="mb-2">
                                    <label class="text-body-secondary small d-flex justify-content-between align-items-center">
                                       <span>Tags</span>
                                       <button class="btn btn-sm btn-outline-secondary mt-2" onclick="openTagsModal()">Edit</button>
                                    </label>
                                    <div class="mt-1" id="currentTags">
                                       <span class="text-body-secondary">No tags</span>
                                    </div>
                                 </div>
                                 
                              </div>
                           </div>
                           <hr class="m-0">
                           <!-- Email View : Content-->
                          
                        </div>
                        <!-- /Email View -->
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="layout-overlay layout-menu-toggle"></div>
   </div>

 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/jquery.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/popper.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/bootstrap.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/perscroll.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/hammer.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/pageauth.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/i18n.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/menu.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/main.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/apexcharts.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/custom.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/toast.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/toast2.js"></script>
   <script src="<?php  echo BASE_ASSET; ?>/js/datatablebs5.js"></script> 
    <script src="<?php  echo BASE_ASSET; ?>/js/select2.js"></script> 
   <script src="<?php  echo BASE_ASSET; ?>/js/tagify.js"></script>   

   <!-- Edit Tags Modal -->
   <div class="modal fade" id="editTagsModal" tabindex="-1" aria-labelledby="editTagsModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="editTagsModalLabel">Edit Tags</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <div class="mb-2">
                  <label class="form-label">Current Tags</label>
                  <div id="currentTagsDisplay" class="border rounded p-2 bg-light">
                     <!-- Current tags will be displayed here -->
                  </div>
               </div>
               <div class="mb-2">
                  <label class="form-label">Edit Tags</label>
                  <input id="TagifyTagsInputViewTicket" name="tags" class="form-control" value="" placeholder="Add or edit tags...">
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
               <button type="button" class="btn btn-primary" id="confirmTagsBtn">Save Tags</button>
            </div>
         </div>
      </div>
   </div>

   <style>
   /* Custom styles for ticket view */
   .app-email {
      min-height: calc(100vh - 200px);
      height: calc(100vh - 200px);
      overflow: hidden;
   }
   
   .app-email-sidebar {
      width: 230px;
      min-width: 230px;
      height: calc(100vh - 200px);
      overflow: hidden;
      display: flex;
      flex-direction: column;
   }
   
   .app-email-view {
      width: 300px;
      min-width: 300px;
      height: calc(100vh - 200px);
      overflow: hidden;
      display: flex;
      flex-direction: column;
   }
   
   .app-emails-list {
      height: calc(100vh - 200px);
      overflow: hidden;
      display: flex;
      flex-direction: column;
   }
   
   /* Left Sidebar - Ticket Details Section */
   .email-filters {
      flex: 1;
      overflow-y: auto;
      overflow-x: hidden;
      scrollbar-width: thin;
      scrollbar-color: #cbd5e1 transparent;
   }
   
   .email-filters::-webkit-scrollbar {
      width: 4px;
   }
   
   .email-filters::-webkit-scrollbar-track {
      background: transparent;
   }
   
   .email-filters::-webkit-scrollbar-thumb {
      background-color: #cbd5e1;
      border-radius: 2px;
   }
   
   .email-filters::-webkit-scrollbar-thumb:hover {
      background-color: #94a3b8;
   }
   
   /* Only show scrollbar on hover */
   .email-filters:not(:hover)::-webkit-scrollbar-thumb {
      background-color: transparent;
   }
   
   .ticket-info-sidebar {
      padding: 0 1.5rem;
   }
   
   .ticket-info-sidebar .mb-2 {
      border-bottom: 1px solid rgba(67, 89, 113, 0.1);
      padding-bottom: 0.75rem;
   }
   
   .ticket-info-sidebar .mb-2:last-child {
      border-bottom: none;
   }
   
   /* Center Section - Email Conversation */
   .app-email-view-content {
      flex: 1;
      overflow-y: auto;
      overflow-x: hidden;
      scrollbar-width: thin;
      scrollbar-color: #cbd5e1 transparent;
   }
   
   .app-email-view-content::-webkit-scrollbar {
      width: 6px;
   }
   
   .app-email-view-content::-webkit-scrollbar-track {
      background: transparent;
   }
   
   .app-email-view-content::-webkit-scrollbar-thumb {
      background-color: #cbd5e1;
      border-radius: 3px;
   }
   
   .app-email-view-content::-webkit-scrollbar-thumb:hover {
      background-color: #94a3b8;
   }
   
   /* Only show scrollbar on hover */
   .app-email-view-content:not(:hover)::-webkit-scrollbar-thumb {
      background-color: transparent;
   }
   
   /* Right Sidebar - Requester Information Section */
   .sender-info-content {
      font-size: 0.95rem;
      max-height: calc(100vh - 300px);
      overflow-y: auto;
      overflow-x: hidden;
      scrollbar-width: thin;
      scrollbar-color: #cbd5e1 transparent;
   }
   
   .sender-info-content::-webkit-scrollbar {
      width: 4px;
   }
   
   .sender-info-content::-webkit-scrollbar-track {
      background: transparent;
   }
   
   .sender-info-content::-webkit-scrollbar-thumb {
      background-color: #cbd5e1;
      border-radius: 2px;
   }
   
   .sender-info-content::-webkit-scrollbar-thumb:hover {
      background-color: #94a3b8;
   }
   
   /* Only show scrollbar on hover */
   .sender-info-content:not(:hover)::-webkit-scrollbar-thumb {
      background-color: transparent;
   }
   
   /* Ensure fixed header sections don't scroll */
   .btn-compost-wrapper {
      flex-shrink: 0;
   }
   
   .emails-list-header {
      flex-shrink: 0;
   }
   
   .app-email-view-header {
      flex-shrink: 0;
   }
   
   .interaction-history-list {
      max-height: 300px;
      overflow-y: auto;
   }
   
   .email-card-prev .card-body {
      line-height: 1.6;
   }
   
   .email-card-prev .card-body p {
      margin-bottom: 1rem;
   }
   
   .email-card-prev .card-body ul {
      margin-left: 1.25rem;
      margin-bottom: 1rem;
   }
   
   .email-card-prev .card-body ul li {
      margin-bottom: 0.5rem;
   }
   
   /* Loading states */
   .spinner-border {
      width: 3rem;
      height: 3rem;
   }
   
   /* WhatsApp-style chat layout */
   .chat-container {
      max-width: 100%;
      margin: 0 auto;
      padding: 20px;
      height: 100%;
      /* Remove overflow from chat container since parent handles it */
   }
   
   .message-bubble {
      max-width: 70%;
      margin: 10px 0;
      padding: 12px 16px;
      border-radius: 18px;
      position: relative;
      word-wrap: break-word;
      box-shadow: 0 1px 2px rgba(0,0,0,0.1);
   }
   
   .message-bubble.incoming {
      background-color: #f1f3f4;
      color: #000;
      margin-left: 0;
      margin-right: auto;
      border-bottom-left-radius: 4px;
   }
   
   .message-bubble.outgoing {
      background-color: #dcf8c6;
      color: #000;
      margin-left: auto;
      margin-right: 0;
      border-bottom-right-radius: 4px;
   }
   
   .message-meta {
      font-size: 11px;
      opacity: 0.7;
      margin-top: 5px;
      text-align: right;
   }
   
   .message-meta.incoming {
      text-align: left;
   }
   
   .sender-name {
      font-weight: 600;
      font-size: 12px;
      margin-bottom: 2px;
      opacity: 0.8;
   }
   
   .message-content {
      line-height: 1.4;
      margin: 2px 0 5px 0;
      font-size: 14px;
   }
   
   /* Direction badges */
   .badge.bg-label-success {
      background-color: rgba(40, 167, 69, 0.1) !important;
      color: #28a745 !important;
   }
   
   .badge.bg-label-primary {
      background-color: rgba(0, 123, 255, 0.1) !important;
      color: #007bff !important;
   }
   
   .badge.bg-label-warning {
      background-color: rgba(255, 193, 7, 0.1) !important;
      color: #ffc107 !important;
   }
   
   /* Disable scrolling for sections with no content */
   .no-scroll {
      overflow: hidden !important;
   }
   
   /* Smooth scrolling */
   .email-filters,
   .app-email-view-content,
   .sender-info-content {
      scroll-behavior: smooth;
   }
   
   @media (max-width: 991px) {
      .app-email-sidebar {
         width: 100%;
         min-width: auto;
         height: auto;
      }
      
      .app-email-view {
         width: 100%;
         min-width: auto;
         height: auto;
      }
      
      .app-emails-list {
         height: auto;
      }
      
      .message-bubble {
         max-width: 85%;
      }
      
      .app-email {
         height: auto;
         min-height: calc(100vh - 200px);
      }
   }
   </style>

   <script>
   // Function to go back to tickets page with auth parameter
   function goBackToTickets() {
      const urlParams = new URLSearchParams(window.location.search);
      const auth = urlParams.get('auth');
      if (auth) {
         window.location.href = `ticket?auth=${auth}`;
      } else {
         window.location.href = 'ticket';
      }
   }

   // Global variables for current user session
   var sessionEmail = '<?php echo $_SESSION['user_email'] ?? ''; ?>';
   var userEmail = '<?php echo $_SESSION['email'] ?? ''; ?>';
   var currentUserId = '<?php echo $_SESSION['user_id'] ?? ''; ?>';

   var tagifyCreate = null, tagifyEdit = null, tagifyUserList = null, tagifyUserListEdit = null, tagifyEmailList = null, tagifyEmailListEdit = null;

   $(document).ready(function() {
      // Initialize Tagify for assignee selection
      if (document.querySelector("#TagifyUserList")) {
        console.log('Initializing TagifyUserList for assignee selection');
        tagifyUserList = new Tagify(document.querySelector("#TagifyUserList"), {
          tagTextProp: "name",
          enforceWhitelist: true,
          skipInvalid: true,
          maxTags: 1,
          dropdown: {
            closeOnSelect: true,
            enabled: 1,
            classname: "users-list",
            searchKeys: ["name", "email"]
          },
          templates: {
            tag: function(tagData) {
              return `
                <tag title="${tagData.title || tagData.email}"
                  contenteditable='false'
                  spellcheck='false'
                  tabIndex="-1"
                  class="${this.settings.classNames.tag} ${tagData.class || ""}"
                  ${this.getAttributes(tagData)}
                >
                  <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
                  <div>
                    <div class='tagify__tag__avatar-wrap'>
                      <span class="avatar avatar-xs bg-primary text-white" style="width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;font-size:11px;font-weight:600;">${tagData.name.substring(0,1).toUpperCase()}</span>
                    </div>
                    <span class='tagify__tag-text'>${tagData.name}</span>
                  </div>
                </tag>
              `;
            },
            dropdownItem: function(tagData) {
              return `
                <div ${this.getAttributes(tagData)}
                  class='tagify__dropdown__item align-items-center ${tagData.class || ""}'
                  tabindex="0"
                  role="option"
                >
                  <div class='tagify__dropdown__item__avatar-wrap' style="margin-right: 8px;">
                    <span class="avatar avatar-xs bg-primary text-white" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;font-size:13px;font-weight:600;">${tagData.name.substring(0,1).toUpperCase()}</span>
                  </div>
                  <div>
                    <div class="fw-medium">${tagData.name}</div>
                    <span style="font-size: 0.85em; color: #666;">${tagData.email}</span>
                  </div>
                </div>
              `;
            }
          },
          whitelist: userListData
        });
      }

      // Load users for tagify
      loadUsersForTagify();

      // Get ticket ID from URL parameter
      const urlParams = new URLSearchParams(window.location.search);
      const encryptedTicketId = urlParams.get('id');
      const auth = urlParams.get('auth');
      
      if (encryptedTicketId) {
         try {
            // Decrypt the ticket ID (base64 decode)
            const ticketId = atob(encryptedTicketId);
            loadTicketData(ticketId);
         } catch (e) {
            console.error('Invalid ticket ID format:', e);
            // Redirect back to tickets page if invalid ID
            goBackToTickets();
         }
      } else {
         // Redirect back to tickets page if no ID provided
         goBackToTickets();
      }
   });

   function loadTicketData(ticketId) {
      // Load ticket details first
      $.ajax({
         url: 'config/api/tickets',
         type: 'POST',
         data: { action: 'getTicketById', id: ticketId },
         success: function(response) {
            if (response.success) {
               const ticket = response.ticket;
                
               $('#ticketNumber').text(ticket.ticket_number || '');
               $('#devTicketNumber').text(ticket.dev_ticket_number ? `#${ticket.dev_ticket_number}` : 'N/A');
               $('#ticketSubject').text(ticket.subject || '');
               $('#ticketRequester').text(`From: ${ticket.requester || ''}`);
               $('#ticketStatus').text(ticket.status || '').removeClass().addClass(getStatusBadgeClass(ticket.status));
               $('#ticketPriority').text(ticket.priority || '').removeClass().addClass(getPriorityBadgeClass(ticket.priority));
               $('#ticketType').text(ticket.type || '');
               
               // Handle assignee
               if (ticket.assignee_name) {
                  const initials = ticket.assignee_name.substring(0, 1).toUpperCase();
                  $('#assigneeAvatar').text(initials);
                  $('#assigneeName').text(ticket.assignee_name);
               } else {
                  $('#assigneeAvatar').text('NA');
                  $('#assigneeName').text('Not Assigned');
               }
               
               // Handle tags
               if (ticket.tags && ticket.tags.trim()) {
                  let tagsHtml = '';
                  try {
                     const tags = JSON.parse(ticket.tags);
                     if (Array.isArray(tags)) {
                        tags.forEach(tag => {
                           tagsHtml += `<span class="badge bg-label-info me-1 mb-1">${tag}</span>`;
                        });
                     }
                  } catch (e) {
                     const tags = ticket.tags.split(',');
                     tags.forEach(tag => {
                        if (tag.trim()) {
                           tagsHtml += `<span class="badge bg-label-info me-1 mb-1">${tag.trim()}</span>`;
                        }
                     });
                  }
                  $('#ticketTags').html(tagsHtml);
               } else {
                  $('#ticketTags').html('<span class="text-body-secondary">No tags</span>');
               }
               
               // Handle CCs
               if (ticket.ccs && ticket.ccs.trim()) {
                  let ccsHtml = '';
                  try {
                     const ccs = JSON.parse(ticket.ccs);
                     if (Array.isArray(ccs)) {
                        ccs.forEach(cc => {
                           ccsHtml += `<small class="text-body d-block">${cc}</small>`;
                        });
                     }
                  } catch (e) {
                     const ccs = ticket.ccs.split(',');
                     ccs.forEach(cc => {
                        if (cc.trim()) {
                           ccsHtml += `<small class="text-body d-block">${cc.trim()}</small>`;
                        }
                     });
                  }
                  $('#ticketCCs').html(ccsHtml);
               } else {
                  $('#ticketCCs').html('<span class="text-body-secondary">No CCs</span>');
               }
               
               // Populate sender info (using requester as sender)
               const senderInitials = ticket.requester ? ticket.requester.substring(0, 2).toUpperCase() : 'NA';
               $('#senderAvatar').text(senderInitials);
               $('#senderName').text(ticket.requester || 'Unknown');
               $('#senderEmail').text(ticket.requester_email || 'Not provided'); 
               $('#requesterEmail').text(ticket.requester_email || ticket.requester || 'Not provided');
               $('#ticketCreatedDate').text(ticket.created_at ? new Date(ticket.created_at).toLocaleDateString() : 'N/A');
               $('#ticketUpdatedDate').text(ticket.updated_at ? new Date(ticket.updated_at).toLocaleDateString() : 'N/A');
               
               // Set current status in status dropdown
               $('#statusChangeSelect').val(ticket.status || 'Open');
               
               // Store current ticket data globally for updates
               window.currentTicket = ticket;
               
               // Update current assignee display in right sidebar
               if (ticket.assignee_name) {
                  const assigneeInitials = ticket.assignee_name.substring(0, 1).toUpperCase();
                  $('#currentAssigneeAvatar').text(assigneeInitials);
                  $('#currentAssigneeName').text(ticket.assignee_name);
               } else {
                  $('#currentAssigneeAvatar').text('NA');
                  $('#currentAssigneeName').text('Not Assigned');
               }
               
               // Update current tags display in right sidebar
               if (ticket.tags && ticket.tags.trim()) {
                  let currentTagsHtml = '';
                  try {
                     const tags = JSON.parse(ticket.tags);
                     if (Array.isArray(tags)) {
                        tags.forEach(tag => {
                           currentTagsHtml += `<span class="badge bg-label-info me-1 mb-1">${tag}</span>`;
                        });
                     }
                  } catch (e) {
                     const tags = ticket.tags.split(',');
                     tags.forEach(tag => {
                        if (tag.trim()) {
                           currentTagsHtml += `<span class="badge bg-label-info me-1 mb-1">${tag.trim()}</span>`;
                        }
                     });
                  }
                  $('#currentTags').html(currentTagsHtml);
               } else {
                  $('#currentTags').html('<span class="text-body-secondary">No tags</span>');
               }
               
               // Populate assignee in Tagify input
               if (tagifyUserList) {
                  isPopulatingAssignee = true;
                  tagifyUserList.removeAllTags();
                  if (ticket.assignee_id && ticket.assignee_name) {
                    // Find the user in userListData or create object
                    let assigneeData = userListData.find(user => user.value == ticket.assignee_id);
                    if (!assigneeData) {
                      // Create assignee data if not found in userListData
                      assigneeData = {
                        value: ticket.assignee_id,
                        name: ticket.assignee_name,
                        email: ticket.assignee_email || ''
                      };
                    }
                    tagifyUserList.addTags([assigneeData]);
                  }
                  isPopulatingAssignee = false;
               }
               
                               // Now load the email/mail content for this ticket
                loadTicketMails(ticketId);
                
                // Check scrollable content after ticket details are loaded
                setTimeout(checkScrollableContent, 100);
               
            } else {
               Swal.fire({
                  icon: 'error',
                  title: 'Error!',
                  text: response.error || 'Failed to load ticket'
               }).then(() => {
                  goBackToTickets();
               });
            }
         },
         error: function() {
            Swal.fire({
               icon: 'error',
               title: 'Error!',
               text: 'Failed to load ticket data'
            }).then(() => {
               goBackToTickets();
            });
         }
      });
   }

   function loadTicketMails(ticketId) {
      // Load all mails/emails for this ticket
      $.ajax({
         url: 'config/api/emailapi',
         type: 'POST',
         data: { action: 'getMailsForTicket', ticket_id: ticketId },
         success: function(response) {
            if (response.success) {
               if (response.mails && response.mails.length > 0) {
                  displayMails(response.mails);
               } else {
                  // No mails found, show default message
                  $('#app-email-view .app-email-view-content').html(`
                     <div class="text-center py-5">
                        <i class="icon-base bx bx-envelope icon-lg text-muted mb-2"></i>
                        <h6 class="text-muted">No email conversation found</h6>
                        <p class="text-body-secondary">This ticket doesn't have any associated email messages.</p>
                     </div>
                  `);
               }
            } else {
               console.error('Failed to load mails:', response.error);
               // Show error message
               $('#app-email-view .app-email-view-content').html(`
                  <div class="text-center py-5">
                     <i class="icon-base bx bx-error-circle icon-lg text-danger mb-2"></i>
                     <h6 class="text-danger">Failed to load email conversation</h6>
                     <p class="text-body-secondary">${response.error}</p>
                  </div>
               `);
            }
         },
         error: function() {
            console.error('Error loading mails for ticket');
            $('#app-email-view .app-email-view-content').html(`
               <div class="text-center py-5">
                  <i class="icon-base bx bx-error-circle icon-lg text-danger mb-2"></i>
                  <h6 class="text-danger">Connection Error</h6>
                  <p class="text-body-secondary">Failed to load email conversation data.</p>
               </div>
            `);
         }
      });
   }

   function displayMails(mails) {
      let mailsHtml = '<div class="chat-container">';
      
      mails.forEach((mail, index) => { 
         const conversations = parseConversation(mail.body_text);
         
         conversations.forEach((conv, convIndex) => {
            if (conv.content && conv.content.trim().length > 0) {
                
               // Determine if this is an admin reply based on the email address
               let isAdminReply = false;
               let senderEmail = conv.email || mail.from_email || 'unknown@example.com';
               let senderName = 'Client';
                
               // Check if the sender email matches current user's email (admin)
               if (conv.email) {
                 isAdminReply = isAdminEmail(conv.email);
                 senderEmail = conv.email;
                 senderName = isAdminReply ? 'Admin' : 'Client';
               } else {
                 isAdminReply = mail.from_email && isAdminEmail(mail.from_email);
                 senderName = isAdminReply ? 'Admin' : 'Client';
               }

               const direction = isAdminReply ? 'outgoing' : 'incoming';

               mailsHtml += createMessageBubble(
                  conv.content,
                  senderName,
                  senderEmail,
                  conv.date || mail.created_at,
                  direction
               );
            }
         });
      });
      
      mailsHtml += '</div>';
      
      // If no content was generated, show a message
      if (mailsHtml === '<div class="chat-container"></div>') {
         mailsHtml = `
            <div class="text-center py-5">
               <i class="icon-base bx bx-envelope icon-lg text-muted mb-2"></i>
               <h6 class="text-muted">No email content found</h6>
               <p class="text-body-secondary">This ticket doesn't have readable email content.</p>
            </div>
         `;
      }
      
             // Update the email view content
       $('#app-email-view .app-email-view-content').html(mailsHtml);
       
       // Check scrollable content after emails are loaded
       setTimeout(checkScrollableContent, 200);
   }

   // FIXED: Parse conversation function for your specific format
   function parseConversation(bodyText) {
      console.log('Parsing conversation from body text:', bodyText);

      const conversations = [];
      
      // Split by double newlines to get individual email entries
      const emailEntries = bodyText.split('\n\n').filter(entry => entry.trim().length > 0);
      
      emailEntries.forEach(entry => {
        const trimmedEntry = entry.trim();
        
        // Match the format: YYYY-MM-DD HH:MM:SS from_email - to_email content
        const emailRegex = /^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\s+([^\s]+@[^\s]+)\s+-\s+([^\s]+@[^\s]+)\s+(.+)$/s;
        const match = trimmedEntry.match(emailRegex);
        
        if (match) {
          const [, datetime, fromEmail, toEmail, content] = match;
          
          conversations.push({
            type: 'Email',
            date: datetime,
            email: fromEmail,
            from: fromEmail,
            to: toEmail,
            content: content.trim()
          });
        } else if (trimmedEntry.length > 0) {
          // Fallback for any content that doesn't match the expected format
          conversations.push({
            type: 'Text',
            date: null,
            email: null,
            from: null,
            to: null,
            content: trimmedEntry
          });
        }
      });

      console.log('Parsed conversations:', conversations);
      return conversations;
   }

   function createMessageBubble(content, senderName, senderEmail, date, direction) {
      let bodyContent = content.trim();
      
      if (!bodyContent || bodyContent.trim().length === 0) {
         bodyContent = '<em>No readable content available</em>';
      }
      
      // Format date to show time  
      let formattedDate;
      if (date && date.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/)) {
         // Date is already in YYYY-MM-DD HH:MM:SS format
         const messageDate = new Date(date);
         const today = new Date();
         
         if (messageDate.toDateString() === today.toDateString()) {
            // Today - show only time
            formattedDate = messageDate.toLocaleTimeString('en-US', {
               hour: '2-digit',
               minute: '2-digit',
               hour12: true
            });
         } else {
            // Other days - show date and time
            formattedDate = messageDate.toLocaleDateString('en-US', {
               month: 'short',
               day: 'numeric',
               hour: '2-digit',
               minute: '2-digit',
               hour12: true
            });
         }
      } else {
         // Fallback for other date formats
         const messageDate = new Date(date);
         formattedDate = messageDate.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
         });
      }
      
      const bubbleClass = direction === 'outgoing' ? 'message-bubble outgoing' : 'message-bubble incoming';
      const metaClass = direction === 'outgoing' ? 'message-meta' : 'message-meta incoming';
      
      return `
         <div class="${bubbleClass}">
            <div class="sender-name">${senderName}</div>
            <div class="message-content">${bodyContent}</div>
            <div class="${metaClass}">${formattedDate}</div>
         </div>
      `;
   }

   function getStatusBadgeClass(status) {
      switch (status?.toLowerCase()) {
         case 'open':
            return 'badge bg-label-success';
         case 'pending':
            return 'badge bg-label-warning';
         case 'on hold':
            return 'badge bg-label-info';
         case 'solved':
            return 'badge bg-label-primary';
         default:
            return 'badge bg-label-secondary';
      }
   }

   function getPriorityBadgeClass(priority) {
      switch (priority?.toLowerCase()) {
         case 'low':
            return 'badge bg-label-success';
         case 'normal':
            return 'badge bg-label-info';
         case 'high':
            return 'badge bg-label-warning';
         case 'urgent':
            return 'badge bg-label-danger';
         default:
            return 'badge bg-label-secondary';
      }
   }

   // Global variables
   var tagifyViewTicket = null;
   var userListData = [];
   var isPopulatingAssignee = false; // Flag to prevent change event during data population

   // Initialize Tagify for tags editing when modal opens
   function initializeTagify() {
     var trendingTags = [
    'Bug', 'Feature Request', 'Enhancement', 'Critical', 'High Priority', 'Medium Priority', 'Low Priority',
    'UI/UX', 'Backend', 'Frontend', 'Database', 'API', 'Authentication', 'Authorization', 'Security',
    'Performance', 'Optimization', 'Mobile', 'Desktop', 'Responsive', 'Cross-browser', 'Compatibility',
    'Testing', 'Unit Testing', 'Integration Testing', 'QA', 'Regression', 'Automation',
    'Documentation', 'Help', 'Tutorial', 'Guide', 'FAQ', 'Support', 'Training',
    'Infrastructure', 'DevOps', 'Deployment', 'CI/CD', 'Server', 'Hosting', 'Cloud',
    'Data Migration', 'Backup', 'Recovery', 'Monitoring', 'Logging', 'Analytics',
    'Third-party', 'Integration', 'Plugin', 'Extension', 'Customization', 'Configuration',
    'User Management', 'Permissions', 'Roles', 'Admin', 'Dashboard', 'Reports',
    'Email', 'Notifications', 'SMS', 'Push Notifications', 'Alerts',
    'Payment', 'Billing', 'Subscription', 'E-commerce', 'Shopping Cart',
    'Search', 'Filter', 'Sort', 'Pagination', 'Navigation', 'Menu',
    'Form', 'Validation', 'Input', 'Upload', 'Download', 'Export', 'Import'
  ];

      if (document.getElementById('TagifyTagsInputViewTicket') && !tagifyViewTicket) {
         tagifyViewTicket = new Tagify(document.getElementById('TagifyTagsInputViewTicket'), {
            whitelist: trendingTags,
            maxTags: 10,
            dropdown: {
        maxItems: 15,
        enabled: 1,
      classname: "tags-inline",
        closeOnSelect: false,
        highlightFirst: true,
        searchKeys: ['value'],
        position: 'auto'
      }
         });
      }
   }

   // Function to update ticket status
   function updateTicketStatus() {
      if (!window.currentTicket) {
         Swal.fire('Error', 'No ticket data available', 'error');
         return;
      }

      const newStatus = $('#statusChangeSelect').val();
      const currentStatus = window.currentTicket.status;

      if (newStatus === currentStatus) {
         Swal.fire('Info', 'Status is already ' + newStatus, 'info');
         return;
      }

      // Show confirmation
      Swal.fire({
         title: 'Update Status?',
         text: `Change ticket status from "${currentStatus}" to "${newStatus}"?`,
         icon: 'question',
         showCancelButton: true,
         confirmButtonColor: '#3085d6',
         cancelButtonColor: '#d33',
         confirmButtonText: 'Yes, update it!'
      }).then((result) => {
         if (result.isConfirmed) {
            // Update via API
            $.ajax({
               url: 'config/api/tickets',
               type: 'POST',
               data: {
                  action: 'updateTicket',
                  id: window.currentTicket.id,
                  subject: window.currentTicket.subject,
                  requester: window.currentTicket.requester,
                  description: window.currentTicket.description,
                  priority: window.currentTicket.priority,
                  type: window.currentTicket.type,
                  status: newStatus,
                  assignee_id: window.currentTicket.assignee_id || '',
                  tags: window.currentTicket.tags || '',
                  ccs: window.currentTicket.ccs || '',
                  dev_ticket_number: window.currentTicket.dev_ticket_number || ''
               },
               success: function(response) {
                  if (response.success) {
                     // Update local data
                     window.currentTicket.status = newStatus;
                     // Update sidebar display
                     $('#ticketStatus').text(newStatus).removeClass().addClass(getStatusBadgeClass(newStatus));
                     Swal.fire('Success!', 'Ticket status updated successfully', 'success');
                  } else {
                     Swal.fire('Error!', response.error || 'Failed to update status', 'error');
                     // Reset dropdown
                     $('#statusChangeSelect').val(currentStatus);
                  }
               },
               error: function() {
                  Swal.fire('Error!', 'Failed to update ticket status', 'error');
                  // Reset dropdown
                  $('#statusChangeSelect').val(currentStatus);
               }
            });
         } else {
            // Reset dropdown
            $('#statusChangeSelect').val(currentStatus);
         }
      });
   }

   // Function to load users for assignee selection
  function loadUsersForTagify() {
    $.ajax({
      url: 'config/api/addusers',
      type: 'POST',
      data: { action: 'listUsers' },
      dataType: 'json',
      success: function (res) {
        if (res.success && res.data) {
          userListData = res.data.map(function(user) {
            return {
              value: user.id,
              name: user.name,
              email: user.email
            };
          });  
          if (tagifyUserList) {
            tagifyUserList.settings.whitelist = userListData;
          }
          if (tagifyUserListEdit) {
            tagifyUserListEdit.settings.whitelist = userListData;
          }
          
          console.log('Users loaded for Tagify:', userListData.length);
        }
      },
      error: function(xhr, status, error) {
        console.error('Failed to load users:', error);
      }
    });
  }

   $(document).on('change', '#TagifyUserList', function() {
     if (isPopulatingAssignee) {
      return;
    }
    
    if (!window.currentTicket || !tagifyUserList) {
      return;
    }

    const selectedUser = tagifyUserList.value && tagifyUserList.value.length > 0 ? tagifyUserList.value[0] : null;
    
    if (!selectedUser) { 
      updateAssignee('', 'Remove Assignee', '');
    } else { 
      updateAssignee(selectedUser.value, selectedUser.name, selectedUser.email);
    }
  });
 
  function updateAssignee(assigneeId, assigneeName, assigneeEmail) {
    if (!window.currentTicket) {
      Swal.fire('Error', 'No ticket data available', 'error');
      return;
    } 
    const actionText = assigneeName === 'Remove Assignee' ? 'remove the assignee' : `assign ticket to ${assigneeName}`;
    
   
        $.ajax({
          url: 'config/api/tickets',
          type: 'POST',
          data: {
            action: 'updateTicket',
            id: window.currentTicket.id,
            subject: window.currentTicket.subject,
            requester: window.currentTicket.requester,
            description: window.currentTicket.description,
            priority: window.currentTicket.priority,
            type: window.currentTicket.type,
            status: window.currentTicket.status,
            assignee_id: assigneeId || '',
            tags: window.currentTicket.tags || '',
            ccs: window.currentTicket.ccs || '',
            dev_ticket_number: window.currentTicket.dev_ticket_number || ''
          },
          success: function(response) {
            if (response.success) { 
              window.currentTicket.assignee_id = assigneeId;
              window.currentTicket.assignee_name = assigneeName === 'Remove Assignee' ? null : assigneeName;

              // Update displays in left sidebar
              if (assigneeName === 'Remove Assignee' || !assigneeName) {
                $('#assigneeAvatar').text('NA');
                $('#assigneeName').text('Not Assigned');
                $('#currentAssigneeAvatar').text('NA');
                $('#currentAssigneeName').text('Not Assigned');
              } else {
                const initials = assigneeName.substring(0, 1).toUpperCase();
                $('#assigneeAvatar').text(initials);
                $('#assigneeName').text(assigneeName);
                $('#currentAssigneeAvatar').text(initials);
                $('#currentAssigneeName').text(assigneeName);
              }

             } else {
              Swal.fire('Error!', response.error || 'Failed to update assignee', 'error');
               resetAssigneeTagify();
            }
          },
          error: function() {
            Swal.fire('Error!', 'Failed to update assignee', 'error');
            // Reset tagify to previous state
            resetAssigneeTagify();
          }
        });
       
  }

  // Function to reset assignee tagify to current state
  function resetAssigneeTagify() {
    if (tagifyUserList && window.currentTicket) {
      isPopulatingAssignee = true;
      tagifyUserList.removeAllTags();
      if (window.currentTicket.assignee_id && window.currentTicket.assignee_name) {
        const currentAssignee = {
          value: window.currentTicket.assignee_id,
          name: window.currentTicket.assignee_name,
          email: window.currentTicket.assignee_email || ''
        };
        tagifyUserList.addTags([currentAssignee]);
      }
      isPopulatingAssignee = false;
    }
  }

// Function to open tags modal
    function openTagsModal() {
      if (!window.currentTicket) {
         Swal.fire('Error', 'No ticket data available', 'error');
         return;
      }

      // Initialize Tagify if not already done
      initializeTagify();

      // Show current tags
      let currentTagsHtml = '';
      if (window.currentTicket.tags && window.currentTicket.tags.trim()) {
         try {
            const tags = JSON.parse(window.currentTicket.tags);
            if (Array.isArray(tags)) {
               tags.forEach(tag => {
                  currentTagsHtml += `<span class="badge bg-label-info me-1 mb-1">${tag}</span>`;
               });
            }
         } catch (e) {
            const tags = window.currentTicket.tags.split(',');
            tags.forEach(tag => {
               if (tag.trim()) {
                  currentTagsHtml += `<span class="badge bg-label-info me-1 mb-1">${tag.trim()}</span>`;
               }
            });
         }
      } else {
         currentTagsHtml = '<span class="text-muted">No tags assigned</span>';
      }

      $('#currentTagsDisplay').html(currentTagsHtml);

      // Set current tags in Tagify
      if (tagifyViewTicket) {
         tagifyViewTicket.removeAllTags();
         if (window.currentTicket.tags && window.currentTicket.tags.trim()) {
            try {
               const tags = JSON.parse(window.currentTicket.tags);
               if (Array.isArray(tags)) {
                  tagifyViewTicket.addTags(tags);
               }
            } catch (e) {
               const tags = window.currentTicket.tags.split(',').map(tag => tag.trim()).filter(tag => tag);
               tagifyViewTicket.addTags(tags);
            }
         }
      }

      // Show modal
      var modal = new bootstrap.Modal(document.getElementById('editTagsModal'));
      modal.show();
   }

   // Handle confirm tags button
   $(document).on('click', '#confirmTagsBtn', function() {
      if (!window.currentTicket || !tagifyViewTicket) {
         Swal.fire('Error', 'No ticket or tags data available', 'error');
         return;
      }

      const newTags = tagifyViewTicket.value ? JSON.stringify(tagifyViewTicket.value.map(tag => tag.value)) : '[]';

      // Update via API
      $.ajax({
         url: 'config/api/tickets',
         type: 'POST',
         data: {
            action: 'updateTicket',
            id: window.currentTicket.id,
            subject: window.currentTicket.subject,
            requester: window.currentTicket.requester,
            description: window.currentTicket.description,
            priority: window.currentTicket.priority,
            type: window.currentTicket.type,
            status: window.currentTicket.status,
            assignee_id: window.currentTicket.assignee_id || '',
            tags: newTags,
            ccs: window.currentTicket.ccs || '',
            dev_ticket_number: window.currentTicket.dev_ticket_number || ''
         },
         success: function(response) {
            if (response.success) {
               // Update local data
               window.currentTicket.tags = newTags;

               // Update displays
               let tagsHtml = '';
               let currentTagsHtml = '';
               const tagsArray = JSON.parse(newTags);
               
               if (tagsArray && tagsArray.length > 0) {
                  tagsArray.forEach(tag => {
                     tagsHtml += `<span class="badge bg-label-info me-1 mb-1">${tag}</span>`;
                     currentTagsHtml += `<span class="badge bg-label-info me-1 mb-1">${tag}</span>`;
                  });
                  $('#ticketTags').html(tagsHtml);
                  $('#currentTags').html(currentTagsHtml);
               } else {
                  $('#ticketTags').html('<span class="text-body-secondary">No tags</span>');
                  $('#currentTags').html('<span class="text-body-secondary">No tags</span>');
               }

               // Close modal
               bootstrap.Modal.getInstance(document.getElementById('editTagsModal')).hide();
               Swal.fire('Success!', 'Tags updated successfully', 'success');
            } else {
               Swal.fire('Error!', response.error || 'Failed to update tags', 'error');
            }
         },
         error: function() {
            Swal.fire('Error!', 'Failed to update tags', 'error');
         }
      });
   });

   // Avatar color generation
   const avatarColors = [
      '#FFB74D', '#64B5F6', '#81C784', '#BA68C8', '#4DD0E1', '#FFD54F',
      '#A1887F', '#90A4AE', '#F06292', '#AED581', '#7986CB', '#E57373'
   ];

   function getAvatarColor(str) {
      let hash = 0;
      for (let i = 0; i < str.length; i++) {
         hash = str.charCodeAt(i) + ((hash << 5) - hash);
      }
      return avatarColors[Math.abs(hash) % avatarColors.length];
   }

       // FIXED: Dynamic admin email detection using session variables
    function isAdminEmail(email) {
      if (!email) return false;
      const emailLower = email.toLowerCase();
      
      console.log('Checking if admin email:', emailLower);
      console.log('Session email:', sessionEmail);
      console.log('User email:', userEmail);
      
      // Primary check: sessionEmail from PHP session
      if (sessionEmail && emailLower.includes(sessionEmail.toLowerCase())) {
        console.log('Matched session email - is admin');
        return true;
      }
      
      // Secondary: userEmail loaded from database
      if (userEmail && emailLower.includes(userEmail.toLowerCase())) {
        console.log('Matched user email - is admin');
        return true;
      }
      
      // Additional admin email patterns (customize as needed)
      const adminPatterns = [
        'admin@',
        'support@',
        'help@',
        'noreply@'
      ];
      
      for (const pattern of adminPatterns) {
        if (emailLower.includes(pattern)) {
          console.log('Matched admin pattern:', pattern, '- is admin');
          return true;
        }
      }
      
      console.log('Not admin email');
      return false;
    }

    // Function to check and control scrolling based on content
    function checkScrollableContent() {
      // Check left sidebar (ticket details)
      const ticketDetails = document.querySelector('.email-filters');
      const ticketDetailsContent = document.querySelector('.ticket-info-sidebar');
      if (ticketDetails && ticketDetailsContent) {
        if (ticketDetailsContent.scrollHeight <= ticketDetails.clientHeight) {
          ticketDetails.classList.add('no-scroll');
        } else {
          ticketDetails.classList.remove('no-scroll');
        }
      }

      // Check center section (email conversation)
      const emailContent = document.querySelector('.app-email-view-content');
      const chatContainer = document.querySelector('.chat-container');
      if (emailContent && chatContainer) {
        if (chatContainer.scrollHeight <= emailContent.clientHeight) {
          emailContent.classList.add('no-scroll');
        } else {
          emailContent.classList.remove('no-scroll');
        }
      }

      // Check right sidebar (requester information)
      const requesterInfo = document.querySelector('.sender-info-content');
      if (requesterInfo) {
        if (requesterInfo.scrollHeight <= requesterInfo.clientHeight) {
          requesterInfo.classList.add('no-scroll');
        } else {
          requesterInfo.classList.remove('no-scroll');
        }
      }
    }

    // Function to add hover-based scroll control
    function initializeHoverScrolling() {
      const scrollableSections = [
        '.email-filters',
        '.app-email-view-content', 
        '.sender-info-content'
      ];

      scrollableSections.forEach(selector => {
        const element = document.querySelector(selector);
        if (element) {
          // Add mouse enter/leave events for visual feedback
          element.addEventListener('mouseenter', function() {
            this.style.scrollbarColor = '#94a3b8 transparent';
          });

          element.addEventListener('mouseleave', function() {
            this.style.scrollbarColor = '#cbd5e1 transparent';
          });

          // Prevent wheel event from bubbling to parent when hovering
          element.addEventListener('wheel', function(e) {
            const isScrollable = this.scrollHeight > this.clientHeight;
            
            if (!isScrollable) {
              e.preventDefault();
              return;
            }

            const isAtTop = this.scrollTop === 0;
            const isAtBottom = this.scrollTop + this.clientHeight >= this.scrollHeight;
            
            // Prevent scrolling parent when at boundaries
            if ((e.deltaY < 0 && isAtTop) || (e.deltaY > 0 && isAtBottom)) {
              // Allow normal scrolling within bounds
              return;
            }
            
            // Stop propagation to prevent other sections from scrolling
            e.stopPropagation();
          }, { passive: false });
        }
      });
    }

    // Initialize scroll controls after DOM is ready
    $(document).ready(function() {
      // Initialize hover scrolling immediately
      initializeHoverScrolling();
      
      // Check scrollable content after a short delay to ensure content is loaded
      setTimeout(checkScrollableContent, 100);
      
      // Recheck when window is resized
      $(window).on('resize', checkScrollableContent);
    });
   </script>
</body>
</html>