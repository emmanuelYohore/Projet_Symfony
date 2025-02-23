# Projet_Symfony

## Overview

Projet_Symfony is a web application built using the Symfony framework. The application is designed to help users manage their habits and tasks, both individually and within groups. Users can create, complete, and delete habits, track their progress, and view their points. The application also supports group functionality, allowing users to create groups, add members, and manage group habits.

## Features

- User registration and login
- Create, complete, and delete habits
- Track habit completion and points
- Group functionality: create groups, add members, manage group habits
- User and group points tracking
- Responsive design using Bootstrap

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/yourusername/Projet_Symfony.git
    cd Projet_Symfony/symfony_Final
    ```

2. Install mongodb 
    Go to this website: [https://pecl.php.net/package/mongodb](https://pecl.php.net/package/mongodb)

    ### For Windows:
    - Download the appropriate release for your PHP version.
    - Go to the `php/ext` directory and put the downloaded file in it.
    - In your `php.ini` file, add the following line:
      ```ini
      extension=php_mongodb.dll
      ```

    ### For Linux:
    - Download the appropriate release for your PHP version.
    - Go to the `php/ext` directory and put the downloaded file in it.
    - In your `php.ini` file, add the following line:
      ```ini
      extension=mongodb.so
      ```
3. Install dependencies:
    ```bash
    composer install
    npm install
    ```

4. Start the development server:
    ```bash
    symfony server:start
    ```

5. Access the application in your browser at `http://127.0.0.1:8000`.

6. Every day at 4:00pm launch the command symfony bin/console app:clean-habit-completions it will check if every habit are done in the good period or not and reset the creation of habits

## Usage

### User Registration and Login

- Users can register by providing their first name, last name, username, email, and password.
- After registration, users can log in using their username or email and password.

### Managing Habits

- Users can create new habits by providing a name, description, difficulty, and periodicity.
- Users can complete habits by checking the checkbox next to the habit.
- Users can delete habits if they no longer want to track them.

### Group Functionality

- Users can create groups and become the group creator.
- Group creators can add members to the group by providing their email addresses.
- Group creators can create group habits that all group members can complete.
- Group points are tracked based on the completion of group habits.

## Contributing

Contributions are welcome! Please fork the repository and create a pull request with your changes.

## License

This project is licensed under the MIT License. See the LICENSE file for details.

## Acknowledgements

- Symfony Framework
- Bootstrap for responsive design
- Doctrine MongoDB ODM for database management