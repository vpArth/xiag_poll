### [Test task](https://test-task.xiag.ch/fullstack-developer.html) ###

#### Using provided templates, develop the poll site ####

User’s stories:

 1. Site visitor enters a question, unlimited count of possible answers (at least two) and presses [start] button to begin voting.  

    [HTML code example 1](https://test-task.xiag.ch/fullstack-developer__example1.html)

 2. Each poll has its own homepage addressed by a secret unique URI. After "start" button press browser redirects to poll home page. Users can copy-paste this link to send it to others and invite them to participate.  
 3. To vote a person should enter his/her name (mandatory), select a preferred option and submit his decision. It is possible to vote only once for a person using one browser. In the bottom of page there is voting results table, refreshed in real time. When a person makes his decision, all participants should see this in the results table.  

    [HTML code example 2](https://test-task.xiag.ch/fullstack-developer__example1.html)


#### Requirements:
  a. Don't use any frameworks  
  b. Use PHP or Node.js  

#### Nice to have:
  a. Support of all modern browsers  
  b. Tests  

#### Expected result:
  a. Source code  
  b. System requirements and installation instructions on our platform, in English.  


_______

#### Installation instructions

```shell script
git clone vpArth/xiag_poll && cd xiag_poll

composer install --no-dev --optimize-autoloader

# Create and edit .env.local, if you want non-default database(Default is «sqlite:/tmp/xiag_poll.db»)
bin/install.php # Create project schema

# Setup any webserver to serve that project with document root in public and front controller public/index.php
php -S 0.0.0.0:1080 -t public # for example
```
