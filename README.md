<div align="center">
<img src="assets/images/logo2.png" alt="PayStream Logo" width="320"/>
<h1> PayStream: Effortless Payroll in the Age of Automation </h1>
</div>

## Description
PayStream is a comprehensive payroll management system designed to streamline the payroll process for businesses. It allows for efficient management of employee data, clocking, timecards, and payroll processing.

## Key Features
- **Add Employee**: Add new employees to the system.
- **Manage Employees**: View and manage existing employees.
- **Manage Clocking**: View and manage clocking data.
- **Time Card**: View and manage time cards.
- **Process Payroll**: Process payroll for all employees for a selected month.
- **View Payslips**: View and download payslips for employees.

## Technologies Used
- **Backend**: PHP
- **Frontend**: HTML, CSS, JavaScript, Bootstrap
- **Database**: MySQL
- **PDF Generation**: FPDF

## Getting Started
### Prerequisites
- XAMPP or any other local server environment
- Composer for dependency management

### Installation
1. Clone the repository:
    ```bash
    git clone https://github.com/fadesignlk/paystream.git
    ```
2. Navigate to the project directory:
    ```bash
    cd paystream
    ```
3. Install dependencies:
    ```bash
    composer install
    ```
4. Set up the database:
    - Import the `database.sql` file located in the `database` directory into your MySQL database.
    - Update the database configuration in `config.php`.

5. Start the local server:
    - Open XAMPP and start Apache and MySQL.

6. Access the application:
    - Open your web browser and navigate to `http://localhost/paystream`.

## Contributing
We welcome contributions to enhance the functionality of PayStream. To contribute, please follow these steps:
1. Fork the repository.
2. Create a new branch:
    ```bash
    git checkout -b feature/your-feature-name
    ```
3. Make your changes and commit them:
    ```bash
    git commit -m "Add your commit message"
    ```
4. Push to the branch:
    ```bash
    git push origin feature/your-feature-name
    ```
5. Open a pull request.

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.