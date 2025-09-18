create table team(


);

create table course(

);


create table course_materials(
    `id` int unsigned not null primary key autoincrement,
    `course_id` int not null,
    `git_id` varchar(64) not null,
    `status` enum('')

);


create table course_members(


);

create table course_comments(


);
