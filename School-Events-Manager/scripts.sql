create table events(
   id integer unsigned auto_increment not null,
   startdate date not null,
   starttime time not null,
   endtime time not null,
   title varchar(100) not null,
   place varchar(30) not null,
   status bit not null,
   override bit not null,
   primary key(id)
);

create table weather(
   id integer unsigned not null,
   wtype varchar(20) not null,
   status bit,
   val integer,
   foreign key (id) references events(id),
   primary key(id,wtype)
);


INSERT INTO `events` (`id`, `startdate`, `starttime`, `endtime`, `title`, `place`, `status`,`override`) VALUES (NULL, '2021-07-26', '06:00:00', '14:30:00', 'Event 1', 'Place 1', b'1',b'0');
INSERT INTO `events` (`id`, `startdate`, `starttime`, `endtime`, `title`, `place`, `status`,`override`) VALUES (NULL, '2021-07-30', '12:00:00', '4:00:00', 'Event 2', 'Place 2', b'1',b'0');