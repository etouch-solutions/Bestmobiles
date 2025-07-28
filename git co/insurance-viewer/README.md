# Insurance Viewer Project

## Overview
The Insurance Viewer project is a web application that allows users to view insurance entries stored in a MySQL database. The application fetches data from the database and displays it in a user-friendly format.

## Project Structure
```
insurance-viewer
├── public
│   ├── index.php
│   └── styles.css
├── src
│   ├── db.php
│   └── fetch_insurance_entries.php
├── .gitignore
└── README.md
```

## Setup Instructions

1. **Clone the Repository**
   Clone the repository to your local machine using:
   ```
   git clone <repository-url>
   ```

2. **Install Dependencies**
   Ensure you have a local server environment set up (e.g., XAMPP, MAMP) to run PHP applications.

3. **Database Configuration**
   - Create a MySQL database and import the necessary tables for the insurance entries.
   - Update the `src/db.php` file with your database credentials.

4. **Access the Application**
   - Place the project folder in the appropriate directory for your local server (e.g., `htdocs` for XAMPP).
   - Open your web browser and navigate to `http://localhost/insurance-viewer/public/index.php` to view the application.

## Usage
- The main page (`index.php`) will display all insurance entries fetched from the database.
- The application is styled using the `styles.css` file located in the `public` directory.

## Contributing
Contributions are welcome! Please feel free to submit a pull request or open an issue for any enhancements or bug fixes.

## License
This project is open-source and available under the MIT License.