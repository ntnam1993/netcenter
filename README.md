# README #

This README would normally document whatever steps are necessary to get your application up and running.

## Install source

#### 1. Clone
##### ssh 
```
    git clone git@bitbucket.org:work-house/netcenter-server.git
```

##### https
```
    git clone https://nguyenthanhnam1993@bitbucket.org/work-house/netcenter-server.git
```

### 2. Run Docker
##### Before start, Install Docker in your enviroment and start it

```
    cd netcenter-server
```

##### Let's start build docker image

```
    docker-compose build --no-cache
```

##### Let's start build docker container from image

```
    docker-compose build --no-cache
```

### 3. Access Docker to build dependency Lumen

##### Access Docker

```
    docker-compose exec --user=netcenter web bash
```

##### Create env file

```
    cp .env.example .env
```

##### Install dependency

```
    composer install
```

## Push file with minio

###### Open browser with localhost:9000, after that login with accesskey/secretkey and create folder with name is images

### On MacOs

```
    docker ps
    docker inspect <ContainerID>
```

###### In NetworkSettings, find IPAddress and replace it to AWS_ENDPOINT in env file

### On Linux

```
    ifconfig
```

###### Find Docker IP and replace it to AWS_ENDPOINT in env file


###### Open browser and run https://netcenter.test/test to push file and go localhost:9000 test result

## Update/Push Code Bitbucket

##### Check syntax code before update/push

```
    composer lint
```

