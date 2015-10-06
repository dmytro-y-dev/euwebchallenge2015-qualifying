# REST API for job server responsible for downloading images from given URL.

Directories structure
---------------------

`/demo/SimpleClient.php` - demo usage of SchedulerAPI to ask server for downloading a page and getting status.

`/deploy/` - files which are essential for Vagrant environment initialization (nginx configuration and dumps of production and test databases).

`/doxygen/` - doxygen documentation for files from `src/` directory (`.php` files in root folder have description too, but it is not autogenerated).

`/src/` - core source files (HTML files downloading, images parsing, client API, etc.).

`/storage/` - storage of downloaded image files.

`/test/` - unit tests for files from `src/` directory.

`/vendor/` - external libraries.


`/new-job.php` - server script to make a request for downloading HTML file.

`/status.php` - server script to get job's status.

`/worker.php` - server script to perform downloading and parsing of files.

External libraries and tools
----------------------------
- `RabbitMQ` for messages queueing;
- `MySQL Server` to save information about jobs and images;
- `php-curl` to send request from demo application;
- `php-gd` to get images width and height;
- `Doctrine` to provide abstraction layer between implementation and database engine;
- `PHPUnit` as unit testing framework;
- `Doxygen` as documentation generator.

How to run
----------

1. Up Vagrant and change directory to project's root:

`vagrant up`
`vagrant ssh`
`cd /vagrant`

2. Start as many workers as you want:

`php worker.php`

Workers will be waiting for new jobs. After new jobs are arrived, RabbitMQ will send them to workers.

3. Start demo application (a)

`php demo/SimpleClient.php`

or add new job with your own html address (b):

`curl http://localhost/new-job.php?htmlpage=http://www.google.com/`

4. Get work results with:

`curl http://localhost/status.php?job_id=<your_job_id>`

where <your_job_id> is unique job's id from `new-job.php` script (if you have chosen (2.b)).

5. Find results in `storage/` folder.

How to test
-----------

Just run `vendor/bin/phpunit`.

Architecture scheme
-------------------

````

                    --------------          ---------
                   | MySQL Server |  o--o  | Workers |
                    --------------          ---------
                        |  o                  |  o
                        o  |                  o  |
 ---------        -----------------        -----------------
| Clients | o--o | REST API server | ---o | RabbitMQ server |
 ---------        -----------------        -----------------

````

1. Clients send requests to REST API server and get quick responses from it (see `/demo/SimpleClient.php`).
2. REST API server creates new job requests and returns actual jobs statuses (see `/new-job.php`, `/status.php`).
3. RabbitMQ server handles messaging between REST API server and workers, and load balancing workers.
4. Workers do real job on files downloading and parsing (see `/worker.php`).
5. MySQL server is used to store information about jobs and images.
