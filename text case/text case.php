<?php
/*
Plugin Name: Text Manipulation Plugin
Description: A plugin to create a custom admin page with buttons to manipulate heading text and store it in the options table.
Version: 1.0
Author: Your Name
*/

// Add a menu item in the admin dashboard
function text_manipulation_menu_item() {
    add_menu_page(
        'Text Manipulation',
        'Text Manipulation',
        'manage_options',
        'text-manipulation-plugin',
        'text_manipulation_content',
        'dashicons-admin-generic',
        99
    );
}
add_action('admin_menu', 'text_manipulation_menu_item');

// Callback function to display content on the admin page
function text_manipulation_content() {
    // Get the stored text or set a default value
    $stored_text = get_option('manipulated_text', 'Original Heading');
    ?>
    <div class="wrap">
        <h1>Text Manipulation</h1>
        <p>Click buttons to manipulate heading text:</p>

        <h3 id="mainHeading"><?php echo esc_html($stored_text); ?></h3>

        <div>
            <button id="uppercaseButton" class="button">Uppercase</button>
            <button id="lowercaseButton" class="button">Lowercase</button>
            <button id="capitalizeButton" class="button">Capitalize</button>
            <button id="defaultButton" class="button">Default</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('uppercaseButton').addEventListener('click', function() {
                document.getElementById('mainHeading').innerText = document.getElementById('mainHeading').innerText.toUpperCase();
                updateHeadingText(document.getElementById('mainHeading').innerText);
            });

            document.getElementById('lowercaseButton').addEventListener('click', function() {
                document.getElementById('mainHeading').innerText = document.getElementById('mainHeading').innerText.toLowerCase();
                updateHeadingText(document.getElementById('mainHeading').innerText);
            });

            document.getElementById('capitalizeButton').addEventListener('click', function() {
                let text = document.getElementById('mainHeading').innerText;
                document.getElementById('mainHeading').innerText = text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
                updateHeadingText(document.getElementById('mainHeading').innerText);
            });

            document.getElementById('defaultButton').addEventListener('click', function() {
                document.getElementById('mainHeading').innerText = 'Original Heading';
                updateHeadingText(document.getElementById('mainHeading').innerText);
            });

            function updateHeadingText(text) {
                fetch('<?php echo admin_url('admin-post.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=update_manipulated_text&text=' + encodeURIComponent(text)
                });
            }
        });
    </script>
    <?php
}

// Action for updating the manipulated text in options table
function update_manipulated_text() {
    if (isset($_POST['text'])) {
        $text = sanitize_text_field($_POST['text']);
        update_option('manipulated_text', $text);
    }
}
add_action('admin_post_update_manipulated_text', 'update_manipulated_text');
