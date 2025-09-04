<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "portfolio");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch about data
$about_result = $conn->query("SELECT * FROM about WHERE id = 1");
$about_data = $about_result->fetch_assoc();

// Fetch projects data
$projects_result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");

// Fetch reviews data
$reviews_result = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Developer Portfolio - fast, minimal, responsive." />
  <title>Dev Portfolio</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css" />
  <script defer src="app.js"></script>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 256 256'><path fill='%23000000' d='M128 28l84 48v104l-84 48l-84-48V76z'/></svg>">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <!-- Header / Navbar -->
  <header class="site-header" id="top">
    <div class="container nav">
      <a href="#home" class="brand">
        <span class="brand-shape" aria-hidden="true">▢</span>
        <span class="brand-text">Oitijya Islam Auvro</span>
      </a>
      <nav class="nav-links" id="navLinks" aria-label="Primary">
        <a href="#home" class="nav-link active">Home</a>
        <a href="#about" class="nav-link">About</a>
        <a href="#skills" class="nav-link">Skills</a>
        <a href="#projects" class="nav-link">Projects</a>
        <a href="#testimonials" class="nav-link">Testimonials</a>
        <a href="#contact" class="nav-link">Contact</a>
        <a href="mailto:oitijya2002@gmail.com" class="btn btn-primary btn-small">Hire Me</a>
      </nav>
      <div class="nav-actions">
        <button id="themeToggle" class="icon-btn" aria-label="Toggle theme" title="Toggle theme">
          <span class="icon">🌙</span>
        </button>
        <button id="menuToggle" class="icon-btn hamburger" aria-label="Open menu" aria-controls="navLinks" aria-expanded="false">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </header>

  <main>
    <!-- Hero -->
    <section class="hero section" id="home">
      <div class="container grid-2">
        <div class="hero-copy">
          <p class="eyebrow">Hello, I'm</p>
          <h1 class="headline">
            <span class="gradient">Oitijya Islam Auvro</span>
            <span class="subline">Computer Science Student & Web Developer</span>
          </h1>
          <p class="lede">
            I am a third-year Computer Science and Engineering student at KUET with a strong passion for
            both web development and data science. I enjoy creating dynamic, interactive websites and
            leveraging data-driven insights to solve real-world problems.
          </p>
          <div class="hero-cta">
            <a href="#projects" class="btn btn-primary">View Projects</a>
            <a href="#contact" class="btn btn-ghost">Get in touch</a>
          </div>
          <ul class="hero-meta">
            <li>🎓 3rd Year CSE Student at KUET</li>
            <li>🔬 Data Science & Analytics</li>
            <li>🎮 Unity Game Development</li>
          </ul>
        </div>
        <div class="hero-art">
          <div class="art-card floating">
            <div class="dots"></div>
            <img src="assets/profile_pic.png" alt="Profile picture" loading="lazy">
          </div>
          <div class="stats reveal">
            <div class="stat"><strong id="yearsExp">2+</strong><span>Years Industry Experience</span></div>
            <div class="stat"><strong id="projectsShipped"><?php echo $projects_result->num_rows; ?>+</strong><span>Projects Shipped</span></div>
            <div class="stat"><strong id="oss">2+</strong><span>Apps Launched in Play Store</span></div>
          </div>
          <!-- Floating elements for visual enhancement -->
          <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- About -->
    <section class="section" id="about">
      <div class="container grid-2">
        <div class="about-media">
          <div class="about-card reveal">
            <img src="<?php echo htmlspecialchars($about_data['about_image'] ?? 'assets/about_pic.jpg'); ?>" alt="Oitijya Islam Auvro - Professional photo" loading="lazy">
          </div>
        </div>
        <div class="about-copy">
          <h2 class="h2 reveal">About</h2>
          <p class="reveal">
            <?php echo htmlspecialchars($about_data['description'] ?? 'I\'m a third-year Computer Science and Engineering student at KUET with a strong passion for both web development and data science.'); ?>
          </p>
          <div class="pill-list reveal">
            <?php 
            $skills = explode(',', $about_data['skills'] ?? 'HTML,CSS,JavaScript,React,Next.js,Python,C++,Unity,React Native');
            foreach($skills as $skill): 
            ?>
            <span class="pill"><?php echo trim(htmlspecialchars($skill)); ?></span>
            <?php endforeach; ?>
          </div>
          <div class="callout reveal">
            <p><strong>Education:</strong> <?php echo htmlspecialchars($about_data['education'] ?? 'Bachelor of Science in CSE at KUET (Current CGPA: 3.62)'); ?></p>
          </div>
        </div>
      </div>
    </section>

    <!-- Skills Section -->
    <section class="section" id="skills">
      <div class="container">
        <div class="section-head">
          <h2 class="h2 reveal">Skills & Expertise</h2>
          <p class="muted reveal">Technologies I work with daily</p>
        </div>
        <div class="skills-grid">
          <div class="skill-category reveal">
            <div class="skill-icon">
              <i class="fas fa-code"></i>
            </div>
            <h3>Frontend Development</h3>
            <p>Creating responsive, interactive user interfaces</p>
            <div class="skill-items">
              <div class="skill-item">
                <span>React/Next.js</span>
                <div class="skill-bar">
                  <div class="skill-progress" data-width="90%"></div>
                </div>
              </div>
              <div class="skill-item">
                <span>JavaScript/TypeScript</span>
                <div class="skill-bar">
                  <div class="skill-progress" data-width="88%"></div>
                </div>
              </div>
              <div class="skill-item">
                <span>CSS/Tailwind</span>
                <div class="skill-bar">
                  <div class="skill-progress" data-width="85%"></div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="skill-category reveal">
            <div class="skill-icon">
              <i class="fas fa-server"></i>
            </div>
            <h3>Programming & Data Science</h3>
            <p>Programming languages and data analysis</p>
            <div class="skill-items">
              <div class="skill-item">
                <span>Python</span>
                <div class="skill-bar">
                  <div class="skill-progress" data-width="85%"></div>
                </div>
              </div>
              <div class="skill-item">
                <span>C++</span>
                <div class="skill-bar">
                  <div class="skill-progress" data-width="80%"></div>
                </div>
              </div>
              <div class="skill-item">
                <span>Java</span>
                <div class="skill-bar">
                  <div class="skill-progress" data-width="75%"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="skill-category reveal">
            <div class="skill-icon">
              <i class="fas fa-tools"></i>
            </div>
            <h3>Game and Mobile Development</h3>
            <p>Game development and mobile application creation</p>
            <div class="skill-items">
              <div class="skill-item">
                <span>Pandas/Fast.ai</span>
                <div class="skill-bar">
                  <div class="skill-progress" data-width="78%"></div>
                </div>
              </div>
              <div class="skill-item">
                <span>React Native</span>
                <div class="skill-bar">
                  <div class="skill-progress" data-width="80%"></div>
                </div>
              </div>
              <div class="skill-item">
                <span>Unity/C#</span>
                <div class="skill-bar">
                  <div class="skill-progress" data-width="75%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Projects -->
    <section class="section" id="projects">
      <div class="container">
        <div class="section-head">
          <h2 class="h2 reveal">Projects</h2>
          <p class="muted reveal">A selection of recent work.</p>
        </div>
        <div class="project-filters reveal">
          <button class="filter-btn active" data-filter="all">All Projects</button>
          <button class="filter-btn" data-filter="web">Web Apps</button>
          <button class="filter-btn" data-filter="mobile">Mobile</button>
          <button class="filter-btn" data-filter="data">Data Science</button>
          <button class="filter-btn" data-filter="game">Games</button>
        </div>
        <div class="projects-grid">
          <?php while($project = $projects_result->fetch_assoc()): ?>
          <!-- Project Card -->
          <article class="project-card reveal" data-category="<?php echo htmlspecialchars($project['category']); ?>">
            <div class="project-overlay">
              <div class="overlay-content">
                <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                <p><?php echo htmlspecialchars($project['description']); ?></p>
                <div class="overlay-actions">
                  <?php if(!empty($project['live_url'])): ?>
                  <a href="<?php echo htmlspecialchars($project['live_url']); ?>" class="btn btn-primary" target="_blank">Live Demo</a>
                  <?php endif; ?>
                  <?php if(!empty($project['code_url'])): ?>
                  <a href="<?php echo htmlspecialchars($project['code_url']); ?>" class="btn btn-ghost" target="_blank">View Code</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <img src="<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" loading="lazy" />
            <div class="pc-body">
              <h3 class="h3"><?php echo htmlspecialchars($project['title']); ?></h3>
              <p><?php echo htmlspecialchars($project['description']); ?></p>
              <div class="tags">
                <?php 
                $tags = explode(',', $project['tags']);
                foreach($tags as $tag): 
                ?>
                <span><?php echo trim(htmlspecialchars($tag)); ?></span>
                <?php endforeach; ?>
              </div>
              <div class="pc-actions">
                <?php if(!empty($project['live_url'])): ?>
                <a href="<?php echo htmlspecialchars($project['live_url']); ?>" class="btn btn-ghost-sm" target="_blank">Live</a>
                <?php endif; ?>
                <?php if(!empty($project['code_url'])): ?>
                <a href="<?php echo htmlspecialchars($project['code_url']); ?>" class="btn btn-ghost-sm" target="_blank">Code</a>
                <?php endif; ?>
              </div>
            </div>
          </article>
          <?php endwhile; ?>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section" id="testimonials">
      <div class="container">
        <div class="section-head">
          <h2 class="h2 reveal">What Clients Say</h2>
          <p class="muted reveal">Feedback from amazing clients</p>
        </div>
        <div class="testimonials-slider">
          <div class="testimonial-track" id="testimonialTrack">
            <?php 
            $testimonial_count = 0;
            $reviews_result->data_seek(0); // Reset result pointer
            while($review = $reviews_result->fetch_assoc()): 
            ?>
            <div class="testimonial reveal">
              <div class="testimonial-content">
                <div class="stars">
                  <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                </div>
                <p>"<?php echo htmlspecialchars($review['review_text']); ?>"</p>
              </div>
              <div class="testimonial-author">
                <img src="<?php echo htmlspecialchars($review['client_image']); ?>" alt="Client" loading="lazy">
                <div>
                  <strong><?php echo htmlspecialchars($review['client_name']); ?></strong>
                  <span><?php echo htmlspecialchars($review['client_title']); ?></span>
                </div>
              </div>
            </div>
            <?php 
            $testimonial_count++;
            endwhile; 
            ?>
          </div>
          <div class="testimonial-dots">
            <?php for($i = 0; $i < $testimonial_count; $i++): ?>
            <button class="dot <?php echo $i === 0 ? 'active' : ''; ?>" data-slide="<?php echo $i; ?>"></button>
            <?php endfor; ?>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact -->
    <section class="section" id="contact">
      <div class="container">
        <div class="section-head">
          <h2 class="h2 reveal">Contact</h2>
          <p class="muted reveal">Have a project in mind? Let's talk.</p>
        </div>
        <div class="contact-wrapper grid-2">
          <div class="contact-info">
            <div class="contact-item reveal">
              <div class="contact-icon">
                <i class="fas fa-envelope"></i>
              </div>
              <div>
                <h4>Email</h4>
                <p>oitijya2002@gmail.com</p>
              </div>
            </div>
            <div class="contact-item reveal">
              <div class="contact-icon">
                <i class="fas fa-phone"></i>
              </div>
              <div>
                <h4>Phone</h4>
                <p>01575094617</p>
              </div>
            </div>
            <div class="contact-item reveal">
              <div class="contact-icon">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <div>
                <h4>Location</h4>
                <p>Khulna, Bangladesh</p>
              </div>
            </div>
            <div class="social-links reveal">
              <a href="https://github.com/AuvroIslam" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="GitHub Profile"><i class="fab fa-github"></i></a>
              <a href="https://www.linkedin.com/in/oitijya-islam-auvro-a252a5325" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn Profile"><i class="fab fa-linkedin"></i></a>
              <a href="https://www.facebook.com/oitijya.islam.auvro" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Facebook Profile"><i class="fab fa-facebook"></i></a>
            </div>
          </div>
          <form class="contact-form reveal" id="contactForm" action="https://formspree.io/f/xkgvgeow" method="POST">
            <div class="form-row-group">
              <div class="form-row">
                <label for="name">Name</label>
                <input id="name" name="name" type="text" placeholder="Your name" required autocomplete="name" />
                <span class="error" data-for="name"></span>
              </div>
              <div class="form-row">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" placeholder="you@example.com" required autocomplete="email" />
                <span class="error" data-for="email"></span>
              </div>
            </div>
            <div class="form-row">
              <label for="message">Message</label>
              <textarea id="message" name="message" rows="5" placeholder="What can I help with?" required></textarea>
              <span class="error" data-for="message"></span>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary">
                <span>Send Message</span>
                <i class="fas fa-paper-plane"></i>
              </button>
              <p class="form-status" id="formStatus" role="status" aria-live="polite"></p>
            </div>
          </form>
        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="container footer-inner">
      <p>© <span id="year"></span> Oitijya Islam Auvro. All rights reserved.</p>
      <div class="footer-links">
        <a href="#top" class="back-to-top">Back to top ↑</a>
      </div>
    </div>
  </footer>

  <!-- Scroll to top button -->
  <button id="scrollTopBtn" class="scroll-top-btn" aria-label="Scroll to top">
    <i class="fas fa-arrow-up"></i>
  </button>
</body>
</html>
<?php $conn->close(); ?>
