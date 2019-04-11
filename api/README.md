# API Documentation

This document is intended to present the API routes and their description.

You can get the PostMan version here : [https://documenter.getpostman.com/view/782336/S17ruTKA](https://documenter.getpostman.com/view/782336/S17ruTKA).

## In any case

All queries are `POST` queries.

If no route were matched or a query is malformed, an error `400` is returned. Else, all responses are `200`.

If a query requires authentication and the authentication fails, a `503` error is returned.

In any case, the response will have theses 3 parameters :

| Key name | Value type | Description |
|----------|-------------|---------|
| error | _boolean_ | True or False depending on if the route returned an error. |
| message | _string_ | Returns an informative message (can be the error or any message to display). |
| data | _string_ | This column will contain the following `Response` data if there's no error. This column is generally empty when an error occured. |

For all routes expect the `Authentication` and `Registration` ones, the queries must contain in their header the token received when authenticated. The header parameter name must be `X-Ov-Token`.

## Registration

### Query

| Endpoint | `/api/auth/register` | Description |
|----------|-------------|-------------|
| email | _string_ ||
| password | _string_ ||
| first_name | _string_ | Optional |
| last_name | _string_ | Optional |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| token | _string_ ||
| expires_at | _datetime_ ||
| ids | _string_ ||
| first_name | _string_ ||
| last_name | _string_ ||
| email | _string_ ||
| username | _string_ ||

## Authentication

### Query

| Endpoint | `/api/auth/login` | Description |
|----------|-------------|-------------|
| username | _string_ | E-mail or username |
| password | _string_ ||

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| token | _string_ ||
| expires_at | _datetime_ ||
| ids | _string_ ||
| first_name | _string_ ||
| last_name | _string_ ||
| email | _string_ ||
| username | _string_ ||

## User information

### Query

| Endpoint | `/api/auth/info` | Description |
|----------|-------------|-------------|
| _Nothing to provide_ |||

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| ids | _string_ ||
| first_name | _string_ ||
| last_name | _string_ ||
| email | _string_ ||
| username | _string_ ||

## Get expiration date

### Query

| Endpoint | `/api/auth/expiration` | Description |
|----------|-------------|-------------|
| _Nothing sent_ |||

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| expires_at | _datetime_ ||

## Adding an avatar to current user

### Query

| Endpoint | `/api/account/avatar/add` | Description |
|----------|-------------|-------------|
| avatar | _file_ | Image |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| uri | _string_ | Link to the image |

## User last avatar

### Query

| Endpoint | `/api/account/avatar` | Description |
|----------|-------------|-------------|
| ids | _string_ ||

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| uri | _string_ | Link to the image |
| added_at | _datetime_ ||

## User notifications

Returns the list of notifications of the currently connected user.

### Query

| Endpoint | `/api/account/notifications` | Description |
|----------|-------------|-------------|
| pagination_start | _int_ | optional, 0 by default |
| interval | _int_ | optional, 10 by default, 50 maximum |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| id | _int_ ||
| message | _string_ ||
| seen | _int_ | 0 = no, 1 = yes |
| target_type | _string_ | "publication" or "user" |
| target_ids | _string_ | "publication" or "user" |
| expires_at | _datetime_ ||

## User notification seen

Sets the notification `seen` state to `1` if seen or `0`.

### Query

| Endpoint | `/api/account/notification/seen` | Description |
|----------|-------------|-------------|
| id | _string_ ||
| seen | _int_ | 1 if seen, 0 if not seen |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No response_ |||

## List users by username

Performs a "LIKE" SQL query to find a username. Returns the 6 most pertinents results.

### Query

| Endpoint | `/api/account/search` | Description |
|----------|-------------|-------------|
| username | _string_ | Username or part of a username to search. | 

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| ids | _string_ ||
| username | _string_ ||

## Cooperatives list

### Query

| Endpoint | `/api/cooperatives` | Description |
|----------|-------------|-------------|
| _No parameter_ |||

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| id | _int_ | Identifier of the cooperative |
| name | _string_ | Name of the cooperative |
| geolocation | _string_ | Geolocation of the cooperative |

## User cooperatives

### Query

| Endpoint | `/api/account/cooperatives` | Description |
|----------|-------------|-------------|
| _No parameter_ |||

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| id | _int_ | Identifier of the cooperative |
| name | _string_ | Name of the cooperative |

## Cooperative information

### Query

| Endpoint | `/api/cooperative` | Description |
|----------|-------------|-------------|
| id | _int_ | id of the cooperative |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| name | _string_ | Name of the cooperative |
| geolocation | _string_ | Geolocation of the cooperative |
| created_at | _datetime(string)_ | Date of addition of the cooperative in the database |

## Roles list

- administrateur
- enseignant
- contrôleur
- correcteur
- commercial
- acheteur
- vendeur

### Query

| Endpoint | `/api/cooperative/roles` | Description |
|----------|-------------|-------------|
| _No parameter_ |||

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| id | _int_ | Role id |
| name | _string_ | Role name |

## User cooperative roles

User roles regarding to a cooperative.

### Query

| Endpoint | `/api/cooperative/roles` | Description |
|----------|-------------|-------------|
| user_ids | _int_ | optional, ids of the user you want to check the roles. By default, will get the ids of the currently connected user |
| cooperative_id | _int_ | id of the cooperative |

### Response

Returns an empty response if no role were found.
Else, the response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| name | _string_ | Role of the user |
| created_at | _datetime(string)_ | Date of addition of the user role in the database |

## Add user to cooperative

### Query

| Endpoint | `/api/cooperative/users/add` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| user_ids | _int_ | optional, ids of the user you want to add to the cooperative |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## Remove user from coopérative

### Query

| Endpoint | `/api/cooperative/users/remove` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| user_ids | _int_ | optional, ids of the user you want to add to the cooperative |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## Add role to user in cooperative

The user adding the role to a user must be an administration member of the cooperative.

### Query

| Endpoint | `/api/cooperative/roles/add` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| user_ids | _int_ | optional, ids of the user you want to check the roles. By default, will get the ids of the currently connected user |
| role_id | _int_ | id of the role to add |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## Remove role to user in cooperative

The user removing the role to a user must be an administration member of the cooperative.

### Query

| Endpoint | `/api/cooperative/roles/remove` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| user_ids | _int_ | optional, ids of the user you want to check the roles. By default, will get the ids of the currently connected user |
| role_id | _int_ | id of the role to remove |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## Formations list

Get all formations existings in the user's cooperatives

### Query

| Endpoint | `/api/formations` | Description |
|----------|-------------|-------------|
|   cooperative_id    |  _int_ |   optional, id of the cooperative you want to see its formations. By default, will get formations in all user's cooperatives |
|   pagination_start    |  _int_ |   optional |
|   interval    |  _int_ |   optional |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| id | _int_ | Formation id |
| name | _string_ | Formation name |
| estimated_duration | _int_ | Formation estimated_duration to finish |
| level | _int_ | Formation level of difficulty |
| cooperative_id | _int_ | Cooperative id of the formation |
| created_at | _datetime(string)_ | Date of addition of the formation in the database |
| updated_at | _datetime(string)_ | Date of modification of the formation in the database |

## Formation detail

Get all detail about a formation in particular

### Query

| Endpoint | `/api/formations` | Description |
|----------|-------------|-------------|
|   formation_id    |  _int_ |   id of the formation  |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| id | _int_ | Formation id |
| name | _string_ | Formation name |
| estimated_duration | _int_ | Formation estimated_duration to finish |
| level | _int_ | Formation level of difficulty |
| cooperative_id | _int_ | Cooperative id of the formation |
| created_at | _datetime(string)_ | Date of addition of the formation in the database |
| updated_at | _datetime(string)_ | Date of modification of the formation in the database |
| chapters | _array(object)_ | List of chapters that the formation contains |

The chapters be an array of objects of the following format:

| Key name | Value type | Description |
|----------|-------------|-------------|
| id | _int_ | chapter id |
| name | _string_ | chapter name |
| type | _string_ | chapter type (lesson, quizz or activity) |
| is_achieved | _boolean_ | return if the user doing the request have done this chapter |

## Add formation in cooperative

The user adding the formation to a cooperative must be an "enseignant" member of this cooperative.

### Query

| Endpoint | `/api/formations/add` | Description |
|----------|-------------|-------------|
|   cooperative_id    |  _int_ |   id of the cooperative  |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## Remove formation in cooperative

The user removing the formation to a cooperative must be an "enseignant" member of this cooperative.

### Query

| Endpoint | `/api/formations/add` | Description |
|----------|-------------|-------------|
|   formation_id    |  _int_ |   id of the formation  |
|   cooperative_id    |  _int_ |   id of the cooperative  |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

===========

## Cooperative tours list

### Query

| Endpoint | `/api/cooperative/tours` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| tour_id | _int_ | identifier of the tour |
| name | _string_ | name of the tour (can be empty) |
| type | _string_ | 'gathering' or 'distribution' |
| created_at | _datetime(string)_ | Date to which the distribution has been created |

## Cooperative tours add

### Query

| Endpoint | `/api/cooperative/tours/add` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| name | _string_ | can be empty, name of the tour to add |
| type | _string_ | 'gathering' or 'distribution' |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| tour_id | _int_ | identifier of the tour created |

## Cooperative tours remove

### Query

| Endpoint | `/api/cooperative/tours/remove` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| tour_id | _int_ | identifier of the tour to remove |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||
