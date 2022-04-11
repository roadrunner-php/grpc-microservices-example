# GRPC microservices example

### Structure

 - `blog` [spiral/app:2.x](https://github.com/spiral/app)
   - `app`
     - `src`
       - `Services` - GRPC service implementation
 - `users` [spiral/app:2.x](https://github.com/spiral/app)
     - `app`
         - `src`
             - `Services` - GRPC service implementation
 - `shared` [spiral/grpc-skeleton](https://github.com/spiral/grpc-skeleton)
   - `proto` - Proto files for GRPC services
   - `services.php` - list of proto files to compile
   - `src`
     - `Services` - compiled GRPC services
 - `docker` - docker containers


### Installation

1. Make sure you have protoc installed and available in your OS.
2. Download `protoc-gen-php-grpc` for compiling  proto files. `./rr get-protoc-binary`.
3. Compile proto files. `./rr compile-proto-files`.
4. Build docker containers `docker compoe build`.


### Usage

You just need to run `docker-compose up` to start the services.

### Api endpoints

#### Users

1. `GET: http://localhost:8081/users` - list of users

```
curl --location --request GET 'http://localhost:8081/users'
```

2. `GET: http://localhost:8081/user/<id>` - Show user with given ID

```
curl --location --request GET 'http://localhost:8081/user/15'
```

3. `POST: http://localhost:8081/user` - Register a new user

```
curl --location --request POST 'http://localhost:8081/user' \
--form 'username="guest"' \
--form 'password="secret"' \
--form 'email="example@site.com"' \
--form 'is_admin="0"'
```

4. `POST: http://localhost:8081/user/auth` - Authenticate user

```
curl --location --request POST 'http://localhost:8081/user/auth' \
--form 'username="mrutherford"' \
--form 'password="secret"'
```

5. `DELETE: http://localhost:8081/user/<id>` - Delete user with given ID

```
curl --location --request DELETE 'http://localhost:8081/user/50'
```

#### Blog

1. `GET: http://localhost:8081/blog` - list of posts

```
curl --location --request GET 'http://localhost:8081/blog'
```

2. `GET: http://localhost:8081/blog/post/<id>` - Show post with given ID

```
curl --location --request GET 'http://localhost:8081/blog/post/15'
```

3. `POST: http://localhost:8081/blog/post/<id>` - Update post with given ID

```
curl --location --request POST 'http://localhost:8081/blog/post/<id>' \
--form 'title="Hello world"' \
--form 'text="Blog post content"'
```

4. `DELETE: http://localhost:8081/blog/post/<id>` - Delete post with given ID

```
curl --location --request DELETE 'http://localhost:8081/blog/post/50'
```

5. `POST: http://localhost:8081/blog/post` - Create a new post

```
curl --location --request POST 'http://localhost:8081/blog/post' \
--form 'title="Hello world"' \
--form 'text="Blog post content"'
```