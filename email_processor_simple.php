<?php
// Email processing script for automatic ticket creation
// This script should be run every 5 minutes via cron job or task scheduler

require_once '../../config/dbC.php';
require_once '../../config/api/emailapi.php';

class EmailProcessor {
    private $conn;
    
    public function __construct() {
        global $servername, $username, $password, $database;
        $this->conn = new mysqli($servername, $username, $password, $database);
        
        if ($this->conn->connect_error) {
            throw new Exception("Database connection failed: " . $this->conn->connect_error);
        }
    }
    
    public function processAllEmailAccounts() {
        try {
            // Get all active email accounts
            $sql = "SELECT * FROM email_accounts WHERE is_active = 1";
            $result = $this->conn->query($sql);
            
            $total_processed = 0;
            $accounts_processed = 0;
            
            while ($account = $result->fetch_assoc()) {
                echo "Processing account: {$account['account_name']} ({$account['email_address']})\n";
                
                try {
                    $processed = $this->processEmailAccount($account);
                    $total_processed += $processed;
                    $accounts_processed++;
                    
                    echo "  Processed {$processed} emails for this account\n";
                    
                } catch (Exception $e) {
                    echo "  Error processing account {$account['email_address']}: " . $e->getMessage() . "\n";
                    continue;
                }
            }
            
            echo "Summary: Processed {$total_processed} emails from {$accounts_processed} accounts\n";
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
    
    private function processEmailAccount($account) {
        // Decrypt passwords
        $imap_password = base64_decode($account['imap_password']);
        
        // Check if IMAP extension is available
        if (!function_exists('imap_open')) {
            throw new Exception('PHP IMAP extension is not enabled');
        }
        
        $total_processed = 0;
        
        // Process both INBOX (incoming) and Sent (outgoing) folders
        $folders_to_process = [
            ['folder' => 'INBOX', 'direction' => 'incoming'],
            ['folder' => '[Gmail]/Sent Mail', 'direction' => 'outgoing'], // Gmail
            ['folder' => 'Sent Items', 'direction' => 'outgoing'], // Outlook
            ['folder' => 'Sent', 'direction' => 'outgoing'] // Generic
        ];
        
        foreach ($folders_to_process as $folder_info) {
            try {
                echo "    Processing folder: {$folder_info['folder']} ({$folder_info['direction']})\n";
                $processed = $this->processFolderEmails($account, $folder_info['folder'], $folder_info['direction']);
                $total_processed += $processed;
                echo "    Processed {$processed} emails from {$folder_info['folder']}\n";
            } catch (Exception $e) {
                echo "    Could not process folder {$folder_info['folder']}: " . $e->getMessage() . "\n";
                continue;
            }
        }
        
        // Update last checked time for this account after processing all folders
        $this->updateLastCheckedTime($account['id']);
        
        return $total_processed;
    }
    
    private function processFolderEmails($account, $folder, $direction) {
        $imap_password = base64_decode($account['imap_password']);
        
        // Connect to specific folder
        $connection_string = "{{$account['imap_host']}:{$account['imap_port']}/imap/ssl}{$folder}";
        $connection = @imap_open($connection_string, $account['imap_username'], $imap_password);
        
        if (!$connection) {
            throw new Exception("Cannot connect to folder {$folder}: " . imap_last_error());
        }
        
        try {
            // Get emails since last check (instead of just UNSEEN)
            $last_checked = $account['last_checked'] ? $account['last_checked'] : date('Y-m-d H:i:s', strtotime('-1 day'));
            $search_criteria = 'SINCE "' . date('j-M-Y', strtotime($last_checked)) . '"';
            
            echo "      Searching emails since: {$last_checked} (criteria: {$search_criteria})\n";
            
            $emails = imap_search($connection, $search_criteria);
            $processed_count = 0;
            
            if ($emails) {
                echo "      Found " . count($emails) . " emails in date range\n";
                foreach ($emails as $email_number) {
                    $header = imap_headerinfo($connection, $email_number);
                    $email_date = isset($header->udate) ? $header->udate : time();
                    $email_date_utc = date('Y-m-d H:i:s', $email_date);
                    $email_date_ist = gmdate('Y-m-d H:i:s', $email_date + (5.5 * 3600));
                    $subject = isset($header->subject) ? $header->subject : '';
                    echo "      [DEBUG] Email #{$email_number} date UTC: {$email_date_utc}, IST: {$email_date_ist}, subject: {$subject}\n";
                    try {
                        $last_check_timestamp = strtotime($account['last_checked']);
                        // Convert email timestamp to IST for proper comparison
                        $email_date_ist_timestamp = $email_date + (5.5 * 3600);
                        
                        // Debug: Show exact comparison values
                        echo "      [COMPARE] Last check timestamp: {$last_check_timestamp} (" . date('Y-m-d H:i:s', $last_check_timestamp) . ")\n";
                        echo "      [COMPARE] Email timestamp UTC: {$email_date} (" . date('Y-m-d H:i:s', $email_date) . ")\n";
                        echo "      [COMPARE] Email timestamp IST: {$email_date_ist_timestamp} (" . date('Y-m-d H:i:s', $email_date_ist_timestamp) . ")\n";
                        
                        // Skip emails that are older than last check time (compare IST to IST)
                        if ($last_check_timestamp && $email_date_ist_timestamp <= $last_check_timestamp) {
                            echo "      Skipping old email from {$email_date_ist} (before last check)\n";
                            continue;
                        }
                        // Get message ID to check if already processed
                        $message_id = isset($header->message_id) ? $header->message_id : null;
                        if ($message_id && $this->isEmailAlreadyProcessed($message_id, $account['id'])) {
                            echo "      Skipping already processed email: {$message_id}\n";
                            continue;
                        }
                        echo "      Processing email from {$email_date_ist}\n";
                        $this->processEmail($connection, $email_number, $account, $direction);
                        $processed_count++;
                    } catch (Exception $e) {
                        echo "      Error processing email #{$email_number}: " . $e->getMessage() . "\n";
                        continue;
                    }
                }
            } else {
                echo "      No new emails found since last check\n";
            }
            
            return $processed_count;
            
        } finally {
            imap_close($connection);
        }
    }
    
    private function processEmail($connection, $email_number, $account, $direction = 'incoming') {
        $header = imap_headerinfo($connection, $email_number);
        // Get raw email body to preserve MIME markers
        $body = imap_body($connection, $email_number);

        // Get message ID for tracking
        $message_id = isset($header->message_id) ? $header->message_id : null;

        // Check if we already processed this email
        if ($message_id && $this->isEmailAlreadyProcessed($message_id, $account['id'])) {
            echo "      Email already processed: {$message_id}\n";
            return;
        }

        // For incoming emails, get sender info
        // For outgoing emails, get recipient info but store as sent from our account
        if ($direction === 'incoming') {
            $from_email = '';
            $from_name = '';
            $to_email = $account['email_address'];
            $to_name = $account['account_name'];
            if (isset($header->from[0])) {
                $from_email = $header->from[0]->mailbox . '@' . $header->from[0]->host;
                $from_name = isset($header->from[0]->personal) ? $header->from[0]->personal : '';
            }
        } else { // outgoing
            $from_email = $account['email_address'];
            $from_name = $account['account_name'];
            $to_email = '';
            $to_name = '';
            if (isset($header->to[0])) {
                $to_email = $header->to[0]->mailbox . '@' . $header->to[0]->host;
                $to_name = isset($header->to[0]->personal) ? $header->to[0]->personal : '';
            }
        }
        
        $subject = isset($header->subject) ? $header->subject : 'No Subject';
        $received_date = isset($header->date) ? $header->date : date('Y-m-d H:i:s');
        
        // Store the email in mails table with direction
        $this->storeEmail($account['id'], $message_id, $from_email, $from_name, $to_email, $to_name, $subject, $body, $received_date, $direction);
        
        // Only create tickets from incoming emails that are NOT replies
        if ($direction === 'incoming') {
            // Check if this is a reply to an existing ticket
            $is_reply = $this->isReplyEmail($subject) || $this->findTicketForEmail($subject, $from_email, $to_email);
            
            if (!$is_reply) {
                $this->createTicketFromEmail($account, $from_email, $from_name, $subject, $body, $message_id);
            } else {
                echo "      Skipping ticket creation - this is a reply to existing conversation\n";
            }
        }
        
        // Mark email as read
        imap_setflag_full($connection, $email_number, "\\Seen");
        
        echo "      Processed {$direction} email: {$subject}\n";
    }
    
    private function updateLastCheckedTime($email_account_id) {
        $sql = "UPDATE email_accounts SET last_checked = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $email_account_id);
        $stmt->execute();
        
        echo "    Updated last checked time for account ID: {$email_account_id}\n";
    }
    
    private function isEmailAlreadyProcessed($message_id, $email_account_id) {
        $sql = "SELECT id FROM mails WHERE message_id = ? AND email_account_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $message_id, $email_account_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    private function storeEmail($email_account_id, $message_id, $from_email, $from_name, $to_email, $to_name, $subject, $body, $received_date, $direction) {
        // Clean the email content (remove quoted parts, HTML, etc.)
        $clean_content = $this->cleanEmailContent($body);
        
        // Format date as requested: YYYY-MM-DD HH:MM:SS
        $email_timestamp = strtotime($received_date);
        $formatted_date = date('Y-m-d H:i:s', $email_timestamp);
        
        // Create the simple formatted entry: datetime from_email - to_email content
        $formatted_entry = "{$formatted_date} {$from_email} - {$to_email} {$clean_content}";

        $ticket_id = $this->findTicketForEmail($subject, $from_email, $to_email);

        if ($ticket_id) {
            // Get existing conversation from the most recent mail
            $existing_mail_sql = "SELECT id, body_text FROM mails WHERE ticket_id = ? ORDER BY created_at DESC LIMIT 1";
            $existing_stmt = $this->conn->prepare($existing_mail_sql);
            $existing_stmt->bind_param('i', $ticket_id);
            $existing_stmt->execute();
            $existing_result = $existing_stmt->get_result();
            
            if ($existing_result->num_rows > 0) {
                $existing_mail = $existing_result->fetch_assoc();
                
                // Parse existing conversation and add new message in chronological order
                $updated_conversation = $this->addToConversationChronologically($existing_mail['body_text'], $formatted_entry, $email_timestamp);

                // Update the existing mail record with the combined conversation
                $update_sql = "UPDATE mails SET body_text = ?, message_id = ?, from_name = ?, from_email = ?, to_name = ?, to_email = ?, updated_at = NOW() WHERE id = ?";
                $update_stmt = $this->conn->prepare($update_sql);
                $update_stmt->bind_param('ssssssi', $updated_conversation, $message_id, $from_name, $from_email, $to_name, $to_email, $existing_mail['id']);
                $update_stmt->execute();
                
                echo "      Updated existing conversation for ticket #{$ticket_id}\n";
                return;
            }
        }
        
        // If no existing conversation found, create new mail record
        $sql = "INSERT INTO mails (email_account_id, ticket_id, message_id, from_email, from_name, to_email, to_name, subject, body_text, direction, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iisssssssss', $email_account_id, $ticket_id, $message_id, $from_email, $from_name, $to_email, $to_name, $subject, $formatted_entry, $direction, $received_date);
        $stmt->execute();
        
        echo "      Stored new {$direction} email in database: {$subject}\n";
    }
    
    /**
     * Clean email content - remove quoted parts, HTML, signatures, MIME encoding
     */
    private function cleanEmailContent($raw_body) {
        // First decode quoted-printable encoding (=XX format)
        $content = quoted_printable_decode($raw_body);
        
        // Decode base64 if present
        if (strpos($content, 'Content-Transfer-Encoding: base64') !== false) {
            // Extract base64 content between boundaries
            if (preg_match('/Content-Transfer-Encoding: base64.*?\n\n(.*?)(?=--|\z)/s', $content, $matches)) {
                $base64_content = str_replace(["\n", "\r"], '', $matches[1]);
                $decoded = base64_decode($base64_content);
                if ($decoded !== false) {
                    $content = $decoded;
                }
            }
        }
        
        // Remove HTML tags
        $content = strip_tags($content);
        
        // Decode HTML entities
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Remove MIME headers and boundaries first
        $content = preg_replace('/Content-Type:.*$/m', '', $content);
        $content = preg_replace('/Content-Transfer-Encoding:.*$/m', '', $content);
        $content = preg_replace('/Content-Disposition:.*$/m', '', $content);
        $content = preg_replace('/--[a-f0-9]{8,}.*$/m', '', $content);
        
        // Split by common quoted content patterns and take only the first part (new message)
        $quote_patterns = [
            '/On\s+.*?wrote:\s*/is',  // "On ... wrote:"
            '/On\s+.*?from\s+.*?to\s+.*?:\s*/is',  // "On ... from ... to ...:"
            '/-----Original Message-----/i',
            '/From:.*?Sent:.*?To:.*?Subject:/s',
            '/________________________________/i',
            '/\n\s*>\s*.*$/s',  // Lines starting with >
            '/--\s*\n/s',  // Email signatures
        ];
        
        foreach ($quote_patterns as $pattern) {
            $parts = preg_split($pattern, $content, 2);
            if (count($parts) > 1 && !empty(trim($parts[0]))) {
                $content = trim($parts[0]);
                break;
            }
        }
        
        // Remove any remaining quoted lines (starting with >)
        $lines = explode("\n", $content);
        $clean_lines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (!empty($trimmed) && !preg_match('/^\s*>/', $line)) {
                $clean_lines[] = $trimmed;
            }
        }
        $content = implode(' ', $clean_lines);
        
        // Clean up excessive whitespace and special characters
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/[^\x20-\x7E\x0A\x0D]/', '', $content); // Remove non-printable chars
        
        // Remove common email artifacts
        $content = preg_replace('/\*[^*]*\*/', '', $content); // Remove *text* formatting
        $content = str_replace(['=20', '=3D', '=C2=A0'], [' ', '=', ' '], $content);
        
        return trim($content);
    }
    
    /**
     * Add new message to conversation in chronological order (oldest first)
     */
    private function addToConversationChronologically($existing_conversation, $new_entry, $new_timestamp) {
        // Split existing conversation into individual entries
        $entries = [];
        
        // Parse existing entries (format: YYYY-MM-DD HH:MM:SS email - email content)
        $lines = explode("\n", $existing_conversation);
        $current_entry = '';
        
        foreach ($lines as $line) {
            // Check if line starts with a timestamp (new entry)
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $line)) {
                if (!empty($current_entry)) {
                    // Extract timestamp from previous entry for sorting
                    preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $current_entry, $matches);
                    $timestamp = strtotime($matches[1]);
                    $entries[] = ['timestamp' => $timestamp, 'content' => trim($current_entry)];
                }
                $current_entry = $line;
            } else {
                $current_entry .= "\n" . $line;
            }
        }
        
        // Add the last entry
        if (!empty($current_entry)) {
            preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $current_entry, $matches);
            $timestamp = strtotime($matches[1]);
            $entries[] = ['timestamp' => $timestamp, 'content' => trim($current_entry)];
        }
        
        // Add new entry
        $entries[] = ['timestamp' => $new_timestamp, 'content' => $new_entry];
        
        // Sort by timestamp (chronological order - oldest first)
        usort($entries, function($a, $b) {
            return $a['timestamp'] - $b['timestamp'];
        });
        
        // Rebuild conversation
        $conversation_parts = [];
        foreach ($entries as $entry) {
            $conversation_parts[] = $entry['content'];
        }
        
        return implode("\n\n", $conversation_parts);
    }
    
    private function findTicketForEmail($subject, $from_email, $to_email) {
        // First try to find by ticket number in subject (most reliable)
        if (preg_match('/\b([A-Z]+-\d+)\b/', $subject, $matches)) {
            $ticket_number = $matches[1];
            $sql = "SELECT id FROM ticket WHERE ticket_number = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('s', $ticket_number);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                echo "      Found ticket by number: {$ticket_number} -> ID {$row['id']}\n";
                return $row['id'];
            }
        }
        
        // Try to find by similar subject (for replies without ticket numbers)
        $clean_subject = preg_replace('/^(Re:|RE:|Fwd?:|FW:|FWD:)\s*/i', '', trim($subject));
        $clean_subject = preg_replace('/\s+\d+$/', '', $clean_subject); // Remove trailing numbers like "48383"
        
        $sql = "SELECT id, ticket_number, requester_email FROM ticket WHERE subject LIKE ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $subject_param = '%' . $clean_subject . '%';
        $stmt->bind_param('s', $subject_param);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo "      Found ticket by subject match: '{$clean_subject}' -> {$row['ticket_number']} (ID {$row['id']})\n";
            return $row['id'];
        }
        
        // Try to find by requester email (for new emails from known customers)
        $sql = "SELECT id FROM ticket WHERE requester_email = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $from_email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo "      Found ticket by requester email: {$from_email} -> ID {$row['id']}\n";
            return $row['id'];
        }
        
        echo "      No existing ticket found for: {$subject} from {$from_email}\n";
        return null;
    }
    
    private function createTicketFromEmail($account, $from_email, $from_name, $subject, $body, $message_id) {
        // Check if this is a reply to an existing ticket
        $ticket_id = $this->findExistingTicket($subject, $message_id);
        
        if ($ticket_id) {
            // Add as comment to existing ticket
            $this->addCommentToTicket($ticket_id, $from_email, $from_name, $body, $message_id);
        } else {
            // Create new ticket
            $this->createNewTicket($account, $from_email, $from_name, $subject, $body, $message_id);
        }
    }
    
    private function findExistingTicket($subject, $message_id) {
        // Look for ticket number in subject (e.g., "Re: TKT-123")
        if (preg_match('/\b([A-Z]+-\d+)\b/', $subject, $matches)) {
            $ticket_number = $matches[1];
            $sql = "SELECT id FROM ticket WHERE ticket_number = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('s', $ticket_number);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row['id'];
            }
        }
        
        return null;
    }
    
    private function addCommentToTicket($ticket_id, $from_email, $from_name, $body, $message_id) {
        $clean_body = $this->cleanEmailContent($body);
        
        $sql = "INSERT INTO ticket_comments (ticket_id, comment_text, is_from_email, email_message_id, from_email, from_name, created_at) 
                VALUES (?, ?, 1, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('issss', $ticket_id, $clean_body, $message_id, $from_email, $from_name);
        $stmt->execute();
        
        // Also update the mail record to link it to the ticket
        $update_sql = "UPDATE mails SET ticket_id = ?, is_processed = 1, processed_at = NOW() WHERE message_id = ?";
        $update_stmt = $this->conn->prepare($update_sql);
        $update_stmt->bind_param('is', $ticket_id, $message_id);
        $update_stmt->execute();
        
        echo "      Added comment to existing ticket #{$ticket_id}\n";
    }
    
    private function createNewTicket($account, $from_email, $from_name, $subject, $body, $message_id) {
        // Generate ticket number
        $ticket_number = $account['ticket_prefix'] . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $customer_name = $from_name ?: $from_email;
        $dev_ticket_number = null;
        if (preg_match('/\b([A-Z0-9\-]{4,})\b/', $subject, $matches)) {
            $dev_ticket_number = $matches[1];
        } elseif (preg_match('/\b([A-Z0-9\-]{4,})\b/', $body, $matches)) {
            $dev_ticket_number = $matches[1];
        }

        // Clean the body content for ticket description
        $clean_description = $this->cleanEmailContent($body);

        // Prepare all required fields for ticket table
        $requester = $customer_name;
        $priority = 'Normal';
        $type = 'Question';
        $status = 'Open';
        $tags = json_encode([]);
        $ccs = json_encode([]);
        $assignee_id = null;
        $admin_id = $account['admin_id'];
        $created_at = date('Y-m-d H:i:s');
        $updated_at = $created_at;
        $original_email_id = $message_id;
        $requester_email = $from_email;
        $auto_created = 1;

        $sql = "INSERT INTO ticket (ticket_number, dev_ticket_number, subject, description, requester, priority, type, status, tags, ccs, assignee_id, admin_id, created_at, updated_at, original_email_id, requester_email, auto_created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'ssssssssssissssii',
            $ticket_number,
            $dev_ticket_number,
            $subject,
            $clean_description,
            $requester,
            $priority,
            $type,
            $status,
            $tags,
            $ccs,
            $assignee_id,
            $admin_id,
            $created_at,
            $updated_at,
            $original_email_id,
            $requester_email,
            $auto_created
        );

        if ($stmt->execute()) {
            $ticket_id = $this->conn->insert_id;
            $update_sql = "UPDATE mails SET ticket_id = ?, is_processed = 1, processed_at = NOW() WHERE message_id = ? AND email_account_id = ?";
            $update_stmt = $this->conn->prepare($update_sql);
            $update_stmt->bind_param('isi', $ticket_id, $message_id, $account['id']);
            $update_stmt->execute();
            echo "    Created new ticket: {$ticket_number} (ID: {$ticket_id})\n";
            if ($account['auto_reply_enabled']) {
                $this->sendAutoReply($account, $from_email, $ticket_number, $subject);
            }
        }
    }
    
    private function sendAutoReply($account, $to_email, $ticket_number, $original_subject) {
        // This would implement SMTP sending - simplified for now
        echo "    Auto-reply would be sent to {$to_email} for ticket {$ticket_number}\n";
        
        // You would implement actual SMTP sending here using PHPMailer or similar
        // $this->sendEmail($account, $to_email, "Re: {$original_subject} [{$ticket_number}]", $auto_reply_body);
    }
    
    public function testEmailAccounts() {
        try {
            $sql = "SELECT id, account_name, email_address, last_checked, is_active FROM email_accounts WHERE is_active = 1";
            $result = $this->conn->query($sql);
            
            echo "Active Email Accounts:\n";
            echo "----------------------\n";
            
            while ($account = $result->fetch_assoc()) {
                echo "Account: {$account['account_name']} ({$account['email_address']})\n";
                echo "  Last Checked: " . ($account['last_checked'] ?: 'Never') . "\n";
                echo "  Status: " . ($account['is_active'] ? 'Active' : 'Inactive') . "\n";
                
                // Check how many emails are in the mails table for this account
                $count_sql = "SELECT COUNT(*) as count FROM mails WHERE email_account_id = ?";
                $stmt = $this->conn->prepare($count_sql);
                $stmt->bind_param('i', $account['id']);
                $stmt->execute();
                $count_result = $stmt->get_result();
                $count_row = $count_result->fetch_assoc();
                echo "  Processed Emails: {$count_row['count']}\n";
                
                // Check for recent emails in mails table
                $recent_sql = "SELECT from_email, subject, created_at FROM mails WHERE email_account_id = ? ORDER BY created_at DESC LIMIT 3";
                $stmt2 = $this->conn->prepare($recent_sql);
                $stmt2->bind_param('i', $account['id']);
                $stmt2->execute();
                $recent_result = $stmt2->get_result();
                
                echo "  Recent Processed Emails:\n";
                if ($recent_result->num_rows > 0) {
                    while ($recent = $recent_result->fetch_assoc()) {
                        echo "    - {$recent['created_at']}: {$recent['from_email']} - {$recent['subject']}\n";
                    }
                } else {
                    echo "    - No emails processed yet\n";
                }
                
                // Show what the search criteria would be
                $last_checked = $account['last_checked'] ? $account['last_checked'] : date('Y-m-d H:i:s', strtotime('-1 day'));
                $search_criteria = 'SINCE "' . date('j-M-Y', strtotime($last_checked)) . '"';
                echo "  Next Search Criteria: {$search_criteria}\n";
                
                echo "\n";
            }
            
        } catch (Exception $e) {
            echo "Error in test mode: " . $e->getMessage() . "\n";
        }
    }
    
    private function isReplyEmail($subject) {
        // Check if subject starts with reply indicators
        return preg_match('/^(Re:|RE:|Fwd?:|FW:|FWD:)/i', trim($subject));
    }
}

// Main execution
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    echo "Starting email processing at " . date('Y-m-d H:i:s') . "\n";
    echo "==========================================\n";
    
    try {
        $processor = new EmailProcessor();
        
        // Check command line arguments
        $test_mode = (isset($argv[1]) && $argv[1] === 'test') || isset($_GET['test']);
        $simulate_mode = (isset($argv[1]) && $argv[1] === 'simulate') || isset($_GET['simulate']);
        
        if ($test_mode) {
            // Test mode - check last checked times and email counts
            echo "Running in TEST mode...\n";
            $processor->testEmailAccounts();
        } elseif ($simulate_mode) {
            // Simulate email for testing
            echo "Running in SIMULATION mode...\n";
            // You could add simulation code here for testing
        } else {
            // Process real emails from all configured accounts
            $processor->processAllEmailAccounts();
        }
        
    } catch (Exception $e) {
        echo "Fatal error: " . $e->getMessage() . "\n";
    }
    
    echo "==========================================\n";
    echo "Email processing completed at " . date('Y-m-d H:i:s') . "\n";
}
?>