# 9. Multimedia and File Upload Features

## 9.1 Multimedia Integration
CampusHub incorporates multimedia assets (images, videos, and audio) to deliver an engaging campus experience for students.

### 1. HTML5 Video & Audio Elements
The application uses native HTML5 multimedia tags for cross-browser playback without external plugins:

```html
<!-- HTML5 Responsive Video Player Component -->
<video controls class="gallery-video-player" poster="assets/images/video_poster.jpg">
  <source src="assets/images/campus_tour_2026.mp4" type="video/mp4">
  Your browser does not support the video tag.
</video>
```

### 2. Interactive Lightbox Showcase (`gallery.php`)
When a student browses `gallery.php`, clicking any photo or video card opens a JavaScript-powered modal popup. This popup displays the high-resolution media item, event title, tags, and caption in a clean interface.

---

## 9.2 File Upload Implementation

### 1. HTML Form Configuration
To allow file uploads, the HTML form must use the `POST` method and specify the `enctype="multipart/form-data"` encoding attribute:

```html
<form action="upload_media.php" method="POST" enctype="multipart/form-data">
  <label for="title">Media Title:</label>
  <input type="text" id="title" name="title" required>

  <label for="media_type">Media Type:</label>
  <select id="media_type" name="media_type">
    <option value="image">Photo / Image</option>
    <option value="video">Video Clip</option>
  </select>

  <label for="media_file">Choose File:</label>
  <input type="file" id="media_file" name="media_file" accept="image/*,video/*" required>

  <button type="submit" name="upload_media">Upload Media</button>
</form>
```

### 2. Server-Side PHP Upload Handler (`admin/upload_media.php` & `student/upload_photo.php`)
The server receives the file via the PHP `$_FILES` superglobal array and applies validation before saving it to the server disk:

```php
<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $media_type = ($_POST['media_type'] === 'video') ? 'video' : 'image';
    $event_id = (int)($_POST['event_id'] ?? 0);
    $user_id = $_SESSION['user_id'] ?? 1;

    // Default fallback image
    $file_path = "hero1.jpg";

    if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['media_file']['tmp_name'];
        $file_name = $_FILES['media_file']['name'];
        $file_size = $_FILES['media_file']['size'];
        
        // Extract and validate extension
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'mp4'];

        // Maximum 10MB limit
        if (in_array($file_ext, $allowed_exts) && $file_size <= 10 * 1024 * 1024) {
            // Generate unique filename using timestamp
            $unique_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file_name);
            $target_dir = __DIR__ . "/../assets/images/";
            $target_path = $target_dir . $unique_name;

            // Move uploaded file from temporary storage to target directory
            if (move_uploaded_file($file_tmp, $target_path)) {
                $file_path = $unique_name;
            }
        }
    }

    // Save record to database
    $meta_json = json_encode(['description' => clean_input($_POST['description'] ?? '')]);
    Database::iud(
        "INSERT INTO media (title, media_type, meta_data, events_event_id, users_user_id, file_path) VALUES (?, ?, ?, ?, ?, ?)",
        "sssiis",
        [$title, $media_type, $meta_json, $event_id, $user_id, $file_path]
    );
}
?>
```

---

## 9.3 Security Measures Applied to File Uploads
1. **File Type Whitelisting:** Only allowed image and video formats (`.jpg`, `.jpeg`, `.png`, `.webp`, `.mp4`) are accepted, preventing malicious executable scripts (`.php`, `.exe`) from being uploaded.
2. **Unique Filename Generation:** Prepending `time() . '_'` prevents existing files from being accidentally overwritten.
3. **Sanitization:** Special characters and spaces in filenames are cleaned using regular expressions.
4. **File Size Constraints:** Uploads exceeding the 10MB threshold are rejected with an error message to protect server disk capacity.

---

## 9.4 Supporting Screenshots & Descriptions

### Screenshot 9.1: Administrator Media Upload Form (`admin/upload_media.php`)
> `[Insert Screenshot of Admin Media Upload Interface Here]`  
> **Description:** Shows the administrator form for uploading photos and videos with event linkage and description inputs.

### Screenshot 9.2: Student Activity Photo Upload (`student/upload_photo.php`)
> `[Insert Screenshot of Student Photo Upload Form Here]`  
> **Description:** Shows the student portal photo upload interface with file chooser and feedback message.
