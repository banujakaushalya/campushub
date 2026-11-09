<?php

require_once __DIR__ . '/SMTP.php';
require_once __DIR__ . '/PHPMailer.php';
require_once __DIR__ . '/Exception.php';
require_once __DIR__ . '/../includes/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper {
    
    // Send password reset code
    public static function sendResetCode($email, $name, $code) {
        try {
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = defined('EMAIL_USER') ? EMAIL_USER : 'btv731563@gmail.com';
            $mail->Password = defined('EMAIL_PASS') ? EMAIL_PASS : 'fqsm ddce csvq myst';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            $sender = defined('EMAIL_USER') ? EMAIL_USER : 'noreply@campushub.com';
            $mail->setFrom($sender, 'CampusHub Security');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'CampusHub Password Reset Code';
            
            $html = "
                <html>
                <body style='font-family:Arial, sans-serif; background-color:#f3f4f6; padding:20px;'>
                    <div style='max-width:500px;margin:20px auto;background:#ffffff;padding:30px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1);'>
                        <div style='background:#ff6b4a;color:white;padding:20px;border-radius:6px;text-align:center;'>
                            <h1 style='margin:0;font-size:24px;'>CampusHub</h1>
                        </div>
                        <p style='margin-top:20px;color:#333333;font-size:15px;'>Hi " . htmlspecialchars($name) . ",</p>
                        <p style='color:#555555;font-size:14px;'>Your password reset verification code is:</p>
                        <div style='font-size:32px;font-weight:bold;letter-spacing:6px;text-align:center;background:#f8fafc;padding:15px;border-radius:6px;margin:20px 0;color:#ff6b4a;font-family:monospace;border:1px solid #e2e8f0;'>$code</div>
                        <p style='color:#555555;font-size:14px;'>This verification code expires in 15 minutes.</p>
                        <p style='color:#777777;font-size:13px;'>If you did not request a password reset, please secure your account immediately.</p>
                        <hr style='border:0;border-top:1px solid #eeeeee;margin:20px 0;'>
                        <p style='color:#999999;font-size:12px;text-align:center;'>© 2026 CampusHub Student Portal</p>
                    </div>
                </body>
                </html>
            ";
            
            $mail->Body = $html;
            
            if ($mail->send()) {
                error_log("Reset email sent to $email");
                return true;
            } else {
                error_log("Reset email failed: " . $mail->ErrorInfo);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Reset email error: " . $e->getMessage());
            return false;
        }
    }

    // Send admin / master verification code
    public static function sendAdminVerificationCode($email, $name, $code) {
        try {
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = defined('EMAIL_USER') ? EMAIL_USER : 'btv731563@gmail.com';
            $mail->Password = defined('EMAIL_PASS') ? EMAIL_PASS : 'fqsm ddce csvq myst';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            $sender = defined('EMAIL_USER') ? EMAIL_USER : 'noreply@campushub.com';
            $mail->setFrom($sender, 'CampusHub Master Gateway');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'CampusHub Master Login Security Code';
            
            $html = "
                <html>
                <body style='font-family:Arial, sans-serif; background-color:#f3f4f6; padding:20px;'>
                    <div style='max-width:500px;margin:20px auto;background:#ffffff;padding:30px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1);'>
                        <div style='background:#171b2a;color:white;padding:20px;border-radius:6px;text-align:center;'>
                            <h1 style='margin:0;font-size:24px;'>Master Control Gateway</h1>
                        </div>
                        <p style='margin-top:20px;color:#333333;font-size:15px;'>Hi " . htmlspecialchars($name) . ",</p>
                        <p style='color:#555555;font-size:14px;'>Your master control 2-Factor authentication login code is:</p>
                        <div style='font-size:32px;font-weight:bold;letter-spacing:6px;text-align:center;background:#f8fafc;padding:15px;border-radius:6px;margin:20px 0;color:#ff6b4a;font-family:monospace;border:1px solid #e2e8f0;'>$code</div>
                        <p style='color:#555555;font-size:14px;'>This code is valid for single-use access.</p>
                        <p style='color:#777777;font-size:13px;'>If you did not attempt to access the Master Control Gateway, please contact system administration.</p>
                        <hr style='border:0;border-top:1px solid #eeeeee;margin:20px 0;'>
                        <p style='color:#999999;font-size:12px;text-align:center;'>© 2026 CampusHub Master Control</p>
                    </div>
                </body>
                </html>
            ";
            
            $mail->Body = $html;
            
            if ($mail->send()) {
                error_log("Admin Email sent to $email");
                return true;
            } else {
                error_log("Admin Email failed: " . $mail->ErrorInfo);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Admin Email error: " . $e->getMessage());
            return false;
        }
    }
}
?>
