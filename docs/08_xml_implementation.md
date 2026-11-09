# 8. XML Implementation

## 8.1 Role of XML in Web Applications
**Extensible Markup Language (XML)** is a platform-independent and standardized data representation format. It is widely used in web development for data interchange between different systems, scheduled reporting, external feeds, and data backups.

In CampusHub, XML is used by the administrative panel (`admin/export_events.php`) to export campus event schedules and associated metadata into a structured, machine-readable format.

---

## 8.2 XML Export Implementation (`admin/export_events.php`)
The XML generation feature utilizes PHP's built-in **`SimpleXMLElement`** extension.

### Implementation Workflow:
1. When an administrator clicks the **"Download XML Export"** button, the request triggers `export_events.php?download=xml`.
2. The server sets appropriate HTTP response headers to inform the browser that an XML file download is being streamed.
3. A root XML element `<events>` is initialized.
4. The script iterates through the MySQL `events` table records and appends child nodes for each event attribute (ID, title, description, schedule, capacity, and parsed category/location metadata).
5. The final XML string is output using `$xml->asXML()`.

```php
<?php
require_once '../includes/db.php';

// Verify Administrator Privileges
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'super_admin', 'moderator'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['download']) && $_GET['download'] === 'xml') {
    // Set headers for XML attachment download
    header('Content-Type: application/xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="events_export_' . date('Ymd_His') . '.xml"');

    // Create Root XML Element
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><events></events>');

    // Query event records from MySQL
    $res = Database::search("SELECT * FROM events ORDER BY event_date ASC");

    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $eventNode = $xml->addChild('event');
            $eventNode->addChild('event_id', (string)$row['event_id']);
            $eventNode->addChild('title', htmlspecialchars((string)$row['title']));
            $eventNode->addChild('description', htmlspecialchars((string)$row['description']));
            $eventNode->addChild('event_date', (string)$row['event_date']);
            $eventNode->addChild('event_start_time', (string)$row['event_start_time']);
            $eventNode->addChild('event_end_time', (string)$row['event_end_time']);
            $eventNode->addChild('img_path', htmlspecialchars((string)$row['img_path']));
            $eventNode->addChild('status', (string)$row['status']);
            $eventNode->addChild('seats', (string)$row['seats']);

            // Parse and nest metadata elements
            $metaNode = $eventNode->addChild('meta_data');
            $meta = !empty($row['meta_data']) ? json_decode($row['meta_data'], true) : [];
            if (is_array($meta)) {
                $metaNode->addChild('category', htmlspecialchars((string)($meta['category'] ?? 'General')));
                $metaNode->addChild('location', htmlspecialchars((string)($meta['location'] ?? 'Campus Main Hall')));
                
                $tagsNode = $metaNode->addChild('tags');
                if (isset($meta['tags']) && is_array($meta['tags'])) {
                    foreach ($meta['tags'] as $tag) {
                        $tagsNode->addChild('tag', htmlspecialchars((string)$tag));
                    }
                }
            }
        }
    }

    // Output formatted XML stream
    echo $xml->asXML();
    exit;
}
?>
```

---

## 8.3 Sample Generated XML Document
Below is a sample of the generated XML output showing correct syntax, element nesting, and character encoding:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<events>
  <event>
    <event_id>1</event_id>
    <title>Annual Inter-Campus Hackathon 2026</title>
    <description>A 24-hour coding marathon bringing together student developers from all faculties.</description>
    <event_date>2026-10-15</event_date>
    <event_start_time>09:00:00</event_start_time>
    <event_end_time>17:00:00</event_end_time>
    <img_path>hackathon2026.jpg</img_path>
    <status>display</status>
    <seats>150</seats>
    <meta_data>
      <category>Technology</category>
      <location>Main Computing Complex - Lab 4</location>
      <tags>
        <tag>Coding</tag>
        <tag>Innovation</tag>
        <tag>Hackathon</tag>
      </tags>
    </meta_data>
  </event>
</events>
```

---

## 8.4 XML Validation
To ensure data integrity, the exported XML follows strict validation principles:
- **Well-Formed XML:** Every open tag has a corresponding closing tag, and all attributes are properly formatted.
- **Character Safety:** Dynamic strings are sanitized with `htmlspecialchars()` to escape special XML characters (`<`, `>`, `&`, `'`, `"`).
- **Interoperability:** The resulting file can be opened, validated, and processed by external XML parsers or spreadsheet tools like Microsoft Excel.
