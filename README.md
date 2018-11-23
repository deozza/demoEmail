DemoEmail
=========

DemoEmail is an PoC (prove of concept) kind application. It's an email client, allowing you to manage your incoming messages. It uses an email API (mailgun here) to get the messages' data and a heroku platform to display them.

Built with
==========
* [Symfony 3.4](https://symfony.com/doc/3.4/setup.html) - PHP framework
* [SQLite](https://www.sqlite.org/index.html) - Database language

## Getting started

### Prerequisites
* [Heroku account](https://www.heroku.com/) (deployment platform)
* [Composer](https://getcomposer.org/) (package manager)
* Emailing API account (Mailgun is recommended)

### Installation

In the directory of your choice
```bash
git clone https://github.com/deozza/demoEmail
```

In the cloned directory
```bash
composer install
```
#### Deployment to Heroku
```bash
heroku create
heroku config:set SYMFONY_ENV=prod
heroku config:set MAILGUN_API_KEY=your_api_key
git push heroku master
```

#### Configuration of the email API (exemple of Mailgun)
To catch your incoming email, you need to link your email address to an emailing API. With that kind service, you can track, send and receive messages. We'll focus on the latter. 

Mailgun API allows you to get the content of an email and all the data related. To do so, you'll need a route. A route is a mechanism triggered when you'll receive emails and allowing you to perform extra functionnality, like forwading the email content to another application. 

To create a route in Mailgun, go to your control panel and navigate to the "Routes" tab.

![Routes control panel](https://help.mailgun.com/hc/article_attachments/360017675233/Route__List_-_Mailgun-nav.png) 

There, click "Create Route" and select "Catch All" in the dropdown menu. Then, in the "Actions" table, click on "Forward" and insert the url of your heroku application corresponding to the email saving action. It should looks like

```
https://myapp.herokuapp.com/email
```

![Catch all](https://help.mailgun.com/hc/article_attachments/360016948114/Route__New_-_Mailgun-expression-type.png)


![Forward](https://help.mailgun.com/hc/article_attachments/360016948214/Route__New_-_Mailgun-actions.png)
Finally, you can add a description and confirm by clicking on "Create a route".