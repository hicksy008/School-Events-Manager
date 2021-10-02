# Context
My school is a busy place, with events happening all the time. Sporting and cultural events happen throughout the week and all over the weekend. Some of the events are inside or under cover, lots are not and so the Brisbane weather plays a big part in our activity program. Currently, many different communication methods are implemented including Twitter, Website updates, APP push notifications and many more. This web app is a proof of concept that uses air quality and weather data from APIs to make justified decisions on whether an event should be cancelled or not. 

# Overview
As this is a proof of concept, only the core functionality of the proposed system is included. Simply put, the web app consists of a main calendar on the front page. This calendar is created using [FullCalendar](https://fullcalendar.io/). The proposed test events for the web app are stored in a database and are fetched by system when needed. Very simple project as most of the assignment was writeup.

# Main Features
* Scheduled Event Checking using windows task scheduler to emulate a cron job
* Implementation of FullCalendar
* Event information shown on modals
