# CompanyS crapper
An assignment repo to test scrapping a website

## Setup Instructions
- In the `.docker` directory, run this command to build the docker containers:
  ```shell
  docker compose up -d --build
  ```
- Get into the docker php container:
  ```shell
  docker exec -it docker_php bash
  ```
- Install the composer packages:
  ```shell
  composer install
  ```
- Run Tests (if you want):
  ```shell
  composer run-tests
  ```

## Usage Guidelines
- After running the service and installing all the packages, visit the 
  [Company Scrapper](http://localhost/view/searchPage.html) page in your browser.
- Get into the `docker_php` container.
- Run the consumer:
  ```shell
  composer run-consumer
  ```
- In the Company Scrapper page in your browser, enter one more registration codes (comma separated) in the top search 
  bar and hit ENTER. You can use this sample string for testing:
  ```text
  156514670,303116155,300150177,305555208,135620439,125409995,234522530,120010775,266659850,300549379,125516431,305738622,303416964,304992596,304720176,302302947
  ```
- In your terminal, where the consumer is running, you should see the company information getting resolved one by one.
- Within few seconds, the consumer will finish resolving all the data.
- Refresh the browser page (or just click the blue `Filter` button in the middle), you should see the fetched data.
- You can perform CRUD operations in the UI, it's interactive.
- Enjoy :)