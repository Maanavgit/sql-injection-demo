FROM mysql:8.0

ENV MYSQL_ROOT_PASSWORD=rootpassword
ENV MYSQL_DATABASE=sqldemo
ENV MYSQL_USER=demo
ENV MYSQL_PASSWORD=demopassword

COPY init.sql /docker-entrypoint-initdb.d/init.sql

EXPOSE 3306
