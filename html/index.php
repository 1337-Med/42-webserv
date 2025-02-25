<?php
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'set_preferences') {
        // Handle session for name
        if (!empty($_POST['user_name'])) {
            $_SESSION['user_name'] = htmlspecialchars($_POST['user_name']);
        } else {
            unset($_SESSION['user_name']);
        }

        // Handle cookie for color
        if (!empty($_POST['user_color'])) {
            $color = htmlspecialchars($_POST['user_color']);
            setcookie('user_color', $color, time() + (86400 * 30), "/"); // 30 days
        } else {
            setcookie('user_color', '', time() - 3600, "/"); // Remove cookie
        }
    };
    // Handle CGI POST
    if (isset($_POST['cgi_action'])) {
        $response = [
            'status' => 'success',
            'message' => 'CGI data received',
            'data' => [
                'name' => $_POST['name'] ?? '',
                'username' => $_POST['username'] ?? ''
            ]
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Handle file upload
if (isset($_FILES['file'])) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir);
    
    $fileName = basename($_FILES['file']['name']);
    $targetPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        $response = ['status' => 'success', 'message' => 'File uploaded successfully'];
    } else {
        $response = ['status' => 'error', 'message' => 'File upload failed'];
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebServ - 42 School Project</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6C5CE7;
            --secondary: #00B894;
            --dark: #2D3436;
            --light: #F8F9FA;
            --accent: #FF7675;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Fira Code', monospace;
        }

        body {
            background: linear-gradient(135deg, var(--dark) 0%, #1E1E1E 100%);
            color: var(--light);
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .title {
            text-align: center;
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            margin: 2rem 0;
            position: relative;
            color: var(--light);
        }

        .title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 200px;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
            animation: titleUnderline 3s ease-in-out infinite;
        }

        @keyframes titleUnderline {
            0%, 100% { opacity: 0.5; width: 200px; }
            50% { opacity: 1; width: 300px; }
        }

        .team-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .member-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .member-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .member-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 1rem auto;
            border: 3px solid var(--primary);
            transition: all 0.3s ease;
            filter: grayscale(20%);
        }

        .member-card:hover .member-img {
            border-color: var(--secondary);
            filter: grayscale(0%);
            transform: scale(1.05);
        }

        .links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin: 1.5rem 0;
        }

        .links a {
            color: var(--light);
            text-decoration: none;
            padding: 0.8rem;
            border-radius: 50%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
        }

        .links a:hover {
            color: var(--secondary);
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }

        .features-section {
            background: linear-gradient(145deg, rgba(108, 92, 231, 0.1), rgba(0, 184, 148, 0.1));
            padding: 2rem;
            border-radius: 15px;
            margin: 3rem 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .test-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1.2rem;
            margin: 2.5rem 0;
        }

        .test-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            padding: 1rem 1.5rem;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .test-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.3);
        }

        .code-section {
            background: rgba(0, 0, 0, 0.3);
            padding: 1.5rem;
            border-radius: 12px;
            margin: 2rem 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        pre {
            font-family: 'Fira Code', monospace;
            color: var(--secondary);
            white-space: pre-wrap;
            overflow-x: auto;
        }

        .upload-form {
            display: none;
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 12px;
            margin-top: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .team-section {
                grid-template-columns: 1fr;
            }

            .member-card {
                margin: 0 0.5rem;
            }

            .test-buttons {
                grid-template-columns: 1fr;
            }
        }

        /* Scroll Animation */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 2rem;
            margin-top: auto;
            background: rgba(0, 0, 0, 0);
        }

        /* Custom Icons */
        .fa-42::before {
            content: "42";
            font-family: 'Fira Code', monospace;
            font-weight: bold;
        }
    </style>
        <!-- Keep existing head content -->
        <style>
        /* Add to existing styles */
        .user-info {
            position: fixed;
            top: 70px;
            right: 20px;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            backdrop-filter: blur(5px);
        }

        .color-preview {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-left: 10px;
            vertical-align: middle;
        }

        .preference-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.9);
            padding: 2rem;
            border-radius: 15px;
            z-index: 1001;
        }
    </style>
</head>
<body>
<?php if (isset($_SESSION['user_name'])): ?>
        <div class="user-info reveal">
            Welcome, <?php echo $_SESSION['user_name']; ?>
            <?php if (isset($_COOKIE['user_color'])): ?>
                <div class="color-preview" style="background: <?php echo $_COOKIE['user_color']; ?>"></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Add preference modal -->
    <div id="preferenceModal" class="preference-modal">
        <form method="POST">
            <h3>Set Preferences</h3>
            <div style="margin: 1rem 0;">
                <label>Name:</label>
                <input type="text" name="user_name" 
                       value="<?php echo $_SESSION['user_name'] ?? ''; ?>">
            </div>
            <div style="margin: 1rem 0;">
                <label>Color:</label>
                <input type="color" name="user_color" 
                       value="<?php echo $_COOKIE['user_color'] ?? '#6C5CE7'; ?>">
            </div>
            <input type="hidden" name="action" value="set_preferences">
            <button type="submit" class="test-btn">Save Preferences</button>
        </form>
    </div>
    <!-- Add these modals -->
    <div id="cgiModal" class="preference-modal">
        <form id="cgiForm">
            <h3>Enter CGI Data</h3>
            <div style="margin: 1rem 0;">
                <label>Name:</label>
                <input type="text" name="name" required>
            </div>
            <div style="margin: 1rem 0;">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            <button type="submit" class="test-btn">Submit</button>
        </form>
    </div>

    <div id="deleteModal" class="preference-modal">
        <form id="deleteForm">
            <h3>Delete Item</h3>
            <div style="margin: 1rem 0;">
                <label>Item Name:</label>
                <input type="text" name="item_name" required>
            </div>
            <button type="submit" class="test-btn">Delete</button>
        </form>
</div>

    <!-- Add this navigation header -->
    <nav class="main-header reveal">
        <div class="nav-container">
            <a href="#about" class="nav-link">About</a>
            <a href="#team" class="nav-link">Team</a>
            <a href="#features" class="nav-link">Features</a>
            <a href="#test" class="nav-link">Tests</a>
            <a href="#config" class="nav-link">Config</a>
        </div>
    </nav>
    <div class="container">
        <h1 class="title reveal">WebServ</h1>

        <!-- Project Description -->
        <div class="project-description reveal" id="about">
            <h2 class="section-title">About the Project</h2>
            <p>WebServ is a custom HTTP server implementation developed as part of the 42 School curriculum. This lightweight web server supports core HTTP features and can be configured through config files.</p>
            <div class="github-repo">
                <a href="[YOUR_GITHUB_REPO_URL]" target="_blank" class="repo-link">
                    <i class="fab fa-github"></i>
                    View on GitHub
                </a>
            </div>
        </div>

        <!-- Project Team -->
        <h2 class="section-title reveal" id="team" >Project Team</h2>
        <div class="team-section" >
            <!-- Team Member 1 -->
            <div class="member-card reveal">
                <img src="./assets/nbenyahy.jpg" alt="Nourddine Benyahya" class="member-img">
                <h3>Nourddine Benyahya</h3>
                <div class="links">
                    <a href="[Github URL]" target="_blank" aria-label="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="[LinkedIn URL]" target="_blank" aria-label="LinkedIn">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="[42 Intra URL]" target="_blank" aria-label="42 Intra">
                        <i class="fa-42"></i>
                    </a>
                </div>
            </div>
            <!-- Repeat for other members -->
            <!-- Team Member 1 -->
            <div class="member-card reveal">
                <img src="./assets/amejdoub.jpg" alt="Anas Mejdoub" class="member-img">
                <h3>Anas Mejdoub</h3>
                <div class="links">
                    <a href="[Github URL]" target="_blank" aria-label="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="[LinkedIn URL]" target="_blank" aria-label="LinkedIn">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="[42 Intra URL]" target="_blank" aria-label="42 Intra">
                        <i class="fa-42"></i>
                    </a>
                </div>
            </div>
            <!-- Team Member 1 -->
            <div class="member-card reveal">
                <img src="./assets/abounab.jpg" alt="Abdellah Bounab" class="member-img">
                <h3>Abdellah Bounab</h3>
                <div class="links">
                    <a href="[Github URL]" target="_blank" aria-label="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/abdellah-bounab" target="_blank" aria-label="LinkedIn">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="[42 Intra URL]" target="_blank" aria-label="42 Intra">
                        <i class="fa-42"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Project Features -->
        <h2 class="section-title reveal" id="features">Key Features</h2>
        <div class="features-section reveal">
            <h2>Project Features</h2>
            <p>Our web server implements:</p>
            <ul>
                <li>HTTP Methods: GET, POST, DELETE</li>
                <li>File Upload Handling</li>
                <li>CGI Implementation (PHP/Python)</li>
                <li>Session Management</li>
                <li>Cookie Handling</li>
                <li>Configuration File Support</li>
            </ul>
        </div>

        <!-- Test Project -->
        <h2 class="section-title reveal"  id="test">Test the Server</h2>
        <div class="test-buttons reveal">
            <button class="test-btn" style="<?php echo isset($_COOKIE['user_color']) ? 
                'background: '.$_COOKIE['user_color'] : ''; ?>" onclick="testMethod('GET')">GET</button>
            <button class="test-btn" style="<?php echo isset($_COOKIE['user_color']) ? 
                'background: '.$_COOKIE['user_color'] : ''; ?>" onclick="testMethod('POST')">POST</button>
            <button class="test-btn" style="<?php echo isset($_COOKIE['user_color']) ? 
                'background: '.$_COOKIE['user_color'] : ''; ?>" onclick="testMethod('DELETE')">DELETE</button>
            <button class="test-btn" style="<?php echo isset($_COOKIE['user_color']) ? 
                'background: '.$_COOKIE['user_color'] : ''; ?>" onclick="document.getElementById('upload-form').style.display='block'">Upload</button>
            <button class="test-btn" style="<?php echo isset($_COOKIE['user_color']) ? 
                'background: '.$_COOKIE['user_color'] : ''; ?>" onclick="testCGI()">Test CGI</button>
            <!-- Upload Form -->
               <form id="upload-form" class="upload-form reveal" action="/" method="POST" enctype="multipart/form-data">
                    <input type="file" name="file">
                    <button type="submit">Upload</button>
                </form>
        <button class="test-btn" onclick="showPreferences()" 
                style="<?php echo isset($_COOKIE['user_color']) ? 
                'background: '.$_COOKIE['user_color'] : ''; ?>">
            Test Session & Cookie
        </button>
        </div>

        <!-- Example Config File -->
        <h2 class="section-title reveal"  id="config">Configuration Example</h2>
        <div class="code-section reveal">
            <pre>
            server : {
                port : 81,
                name : localhost,
                root : html,
                error : {
                    404 : 404.html,
                    500 : 500.html,
                },
                route : {
                    path :  /index.php,
                    allowed_mthods : {
                    POST,
                    GET,
                    },
                    has_cgi : yes,
                    cgi : {
                        .php=cgi-bin/php-cgi,
                        .py=/usr/local/bin/python3,
                    },
                    index : test.html,
                    },
                }
                </pre>
            </div>

            <footer class="reveal">
                <p>🚀 Built with passion by WebServ Team</p>
                <p>📍 42 Network - 2023</p>
            </footer>
    </div>
    <style>
        /* Add header styles */
        .main-header {
            position: fixed;
            top: 0;
            width: 100%;
            /* border-top-left-radius: 10%; */
            background: rgba(25, 25, 25, 0.426);
            backdrop-filter: blur(5px);
            z-index: 1000;
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            gap: 2rem;
            padding: 0 2rem;
        }

        .nav-link {
            color: var(--light);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--secondary);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: all 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
            left: 0;
        }

        .nav-link.active {
            color: var(--primary);
            font-weight: bold;
        }

        /* Add padding to container to account for fixed header */
        .container {
            padding-top: 6rem;
        }

        @media (max-width: 768px) {
            .nav-container {
                gap: 1rem;
                padding: 0 1rem;
                flex-wrap: wrap;
            }
            
            .container {
                padding-top: 8rem;
            }
        }
    </style>
        <script>
            // Add smooth scroll and active link logic
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetSection = document.querySelector(targetId);
                    
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
    
                    // Update active class
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
    
            // Add scroll spy to update active link
            const sections = document.querySelectorAll('[id]');
            const options = {
                threshold: 0.5
            };
    
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if(entry.isIntersecting) {
                        document.querySelectorAll('.nav-link').forEach(link => {
                            link.classList.remove('active');
                            if(link.getAttribute('href') === `#${entry.target.id}`) {
                                link.classList.add('active');
                            }
                        });
                    }
                });
            }, options);
    
            sections.forEach(section => observer.observe(section));
        </script>
    <style>
        /* Add these new styles */
        .section-title {
            font-size: 2.2rem;
            margin: 2rem 0 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary);
            position: relative;
            color: var(--secondary);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100px;
            height: 2px;
            background: var(--secondary);
        }

        .project-description {
            text-align: center;
            margin: 2rem auto;
            max-width: 800px;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
        }

        .project-description p {
            margin: 1rem 0;
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .github-repo {
            margin-top: 1.5rem;
        }

        .repo-link {
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 8px;
            color: var(--light);
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .repo-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.3);
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 1.8rem;
            }
            
            .project-description {
                padding: 1.5rem;
            }
        }
    </style>


<script>
    // Scroll Reveal Animation
    const revealElements = document.querySelectorAll('.reveal');
    const revealOptions = {
        threshold: 0.5
    };

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, revealOptions);

    revealElements.forEach(element => {
        revealObserver.observe(element);
    });

    // Mobile Menu Toggle (Add if needed)
    // Smooth Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>
<script>
        // Add preference modal handling
        function showPreferences() {
            const modal = document.getElementById('preferenceModal');
            modal.style.display = 'block';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('preferenceModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>

<script>
// Modified test functions
function testMethod(method) {
    switch(method) {
        case 'GET':
            window.location.href = 'get_page.html';
            break;
            
        case 'DELETE':
            showDeleteModal();
            break;
    }
}

function testCGI() {
    showCGIModal();
}

// New modal handling functions
function showCGIModal() {
    const modal = document.getElementById('cgiModal');
    modal.style.display = 'block';
}

function showDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'block';
}

// CGI Form handling
document.getElementById('cgiForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('cgi_page.php', {
            method: 'POST',
            body: JSON.stringify(Object.fromEntries(formData)),
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        alert(`CGI Response: ${data.message}\nData: ${JSON.stringify(data.data)}`);
        document.getElementById('cgiModal').style.display = 'none';
    } catch (error) {
        alert('Error submitting CGI data');
    }
});

// Delete Form handling
document.getElementById('deleteForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const itemName = formData.get('item_name');
    
    try {
        const response = await fetch(`${encodeURIComponent(itemName)}`, {
            method: 'DELETE'
        });
        
        if (response.ok) {
            alert(`Item "${itemName}" deleted successfully`);
        } else {
            alert('Delete request failed');
        }
        document.getElementById('deleteModal').style.display = 'none';
    } catch (error) {
        alert('Error making delete request');
    }
});

// File upload handling
// document.getElementById('upload-form').addEventListener('submit', async (e) => {
//     e.preventDefault();
//     const formData = new FormData(e.target);
    
//     try {
//         const response = await fetch('upload_handler.php', {
//             method: 'POST',
//             body: formData
//         });
        
//         const data = await response.json();
//         if (data.status === 'success') {
//             alert('File uploaded successfully!');
//         } else {
//             alert('File upload failed: ' + data.message);
//         }
//         e.target.reset();
//         e.target.style.display = 'none';
//     } catch (error) {
//         alert('Error uploading file');
//     }
// });
</script>
</body>
</html>