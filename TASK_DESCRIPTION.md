## Task Description:

Create a Dockerized stack consisting of Nginx, PHP 8.2, MySQL 8, RabbitMQ, and Redis. Use Symfony 6.2 framework to
create a scrapper that retrieves data from https://rekvizitai.vz.lt/en/company-search/ based on a registration code. The
scrapper should extract the following information from the company profile:

1. Company name,
2. Registration code,
3. VAT,
4. Address,
5. Mobile phone.

Additionally, scrape information about the company's turnover from the "XXX turnover for the
year" table. Implement a search bar that accepts a registration code as input and retrieves the required data. Provide
CRUD functionality for the scraped data, including filtering and pagination. As a bonus, make the search bar capable of
handling multiple registration codes separated by commas and utilize RabbitMQ for query handling in the scrapper.

Task Steps:

1. Set up the Docker environment.
2. Create Dockerfiles for each service (Nginx, PHP 8.2, MySQL 8, RabbitMQ, and Redis).
3. Implement Symfony 6.2 framework and create the scrapper functionality.
4. Extract the required information from the company profile and store it in the database.
5. Implement CRUD functionality for managing the scraped data, including filtering and pagination.
6. Configure the search bar to accept a registration code input and retrieve the required data.
7. Bonus: Enhance the search bar to handle multiple registration codes separated by commas.
8. Integrate RabbitMQ for query handling in the scrapper.
9. Test and validate the solution, ensuring all components work together.
10. Document the setup instructions and usage guidelines.
11. Optional: Implement any additional features or improvements.
12. Review the code, address any issues, and refactor if necessary.
13. Finalize the project and submit the solution.

## Time Estimation for the Task

| Task Step                                                         | Estimated Time |
|-------------------------------------------------------------------|----------------|
| Setup docker env ensuring connectivity                            | 1 hour         |
| Create company profile and turnover models and related migrations | 1 hour         |
| Implement CRUD (backend)                                          | 1 hour         |
| Implement FE for Search and CRUD, with filtering and pagination   | 2 hours        |
| Implement the scrapper                                            | 1 hour         |
| Implement RabbitMq query handling with consumer                   | 2 hours        |
| Writing tests                                                     | 2 hours        |
| Documentation                                                     | 1 hour         |
| Buffer time for optional improvements or fixing unforeseen issues | 3 hours        |
| **Total**                                                         | **14 hours**   |

