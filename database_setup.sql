-- SQL schema for Portfolio Admin Panel (Updated)

CREATE DATABASE IF NOT EXISTS portfolio;
USE portfolio;

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(64) DEFAULT NULL,
    remember_token_created TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Projects table (Updated with new fields)
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    tags TEXT NOT NULL,
    live_url VARCHAR(255),
    code_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- About table (Updated with new fields)
CREATE TABLE IF NOT EXISTS about (
    id INT PRIMARY KEY DEFAULT 1,
    description TEXT,
    skills TEXT,
    education TEXT,
    about_image VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default about record
INSERT INTO about (id, description, skills, education, about_image) 
VALUES (1, 
    'I\'m a third-year Computer Science and Engineering student at KUET with a strong passion for both web development and data science. I enjoy creating dynamic, interactive websites and leveraging data-driven insights to solve real-world problems. Additionally, I have a keen interest in Unity game development.',
    'HTML,CSS,JavaScript,React,Next.js,Python,C++,Unity,React Native',
    'Bachelor of Science in CSE at KUET (Current CGPA: 3.62)',
    'assets/about_pic.jpg'
)
ON DUPLICATE KEY UPDATE 
    description = VALUES(description),
    skills = VALUES(skills),
    education = VALUES(education),
    about_image = VALUES(about_image);

-- Reviews/Testimonials table (Updated with new fields)
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    client_title VARCHAR(255) NOT NULL,
    review_text TEXT NOT NULL,
    rating INT DEFAULT 5 CHECK (rating >= 1 AND rating <= 5),
    client_image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample projects based on your index.html (with updated image paths)
INSERT INTO projects (title, description, image, category, tags, live_url, code_url) VALUES
('Mio', 'React Native and Expo application that connects users based on their favorite TV shows and movies. Available on Google Play Store.', 'assets/mio_logo.jpg', 'mobile', 'React Native,Expo,TypeScript,Play Store', 'https://play.google.com/store/apps/details?id=com.mioapp.social&pli=1', 'https://github.com/AuvroIslam/Mio-typeScript-'),

('GDP vs. Olympic Performance', 'Explored the correlation between GDP and Olympic achievements utilizing Tableau Dashboards and created an interactive React website.', 'assets/olympic_gdp_chart.png', 'data', 'React,Tableau,Data Analysis,Vercel', 'https://olympic-vs-gdp-website.vercel.app/', 'https://github.com/AuvroIslam/Olympic_vs_Gdp'),

('Waste Recognition Model', 'Developed a deep learning model for waste classification using Fast.ai and Hugging Face, deployed with a web application.', 'assets/waste_recognition.jpg', 'data', 'Fast.ai,Hugging Face,Deep Learning,GitHub Pages', 'https://auvroislam.github.io/wasteRecognizer/', 'https://github.com/AuvroIslam/wasteRecognizer'),

('3Knot3', 'Top-down 3D action game inspired by the 7 Bir Sreshtho from the 1971 Bangladesh Liberation War, built in Unity with C#.', 'assets/3knot3_Banner.png', 'game', 'Unity,C#,3D Game', 'https://studio-71.itch.io/3knot3', 'https://github.com/Learnathon-By-Geeky-Solutions/studio71')

ON DUPLICATE KEY UPDATE 
    title = VALUES(title),
    description = VALUES(description),
    image = VALUES(image),
    category = VALUES(category),
    tags = VALUES(tags),
    live_url = VALUES(live_url),
    code_url = VALUES(code_url);

-- Insert sample reviews based on your index.html
INSERT INTO reviews (client_name, client_title, review_text, rating, client_image) VALUES
('Google Play Reviewer', 'Mio App User', 'Mio is a great social app to find friends with similar interests. Highly recommended!', 5, 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=100&auto=format&fit=crop'),

('Client from Fiverr', 'Web Development Project', 'The project was submitted well within the deadline and the quality of work was excellent. Very professional.', 5, 'https://images.unsplash.com/photo-1494790108755-2616b612b5bb?q=80&w=100&auto=format&fit=crop'),

('Jane Doe', 'Data Science Consultant', 'The machine learning model was trained perfectly and achieved impressive accuracy. The deployment was seamless.', 5, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=100&auto=format&fit=crop')

ON DUPLICATE KEY UPDATE 
    client_name = VALUES(client_name),
    client_title = VALUES(client_title),
    review_text = VALUES(review_text),
    rating = VALUES(rating),
    client_image = VALUES(client_image);

-- Create a default admin user (password: admin123)
-- Note: In production, use a stronger password and proper hashing
INSERT INTO admins (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE username = VALUES(username);

-- Display created tables
SHOW TABLES;
