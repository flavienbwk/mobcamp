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
| cooperative_id | _int_ | First cooperative to join |

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

| Endpoint | `/api/roles` | Description |
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
| user_ids | _string_ | optional, ids of the user you want to check the roles. By default, will get the ids of the currently connected user |
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
| user_ids | _string_ | optional, ids of the user you want to add to the cooperative |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## Remove user from coopérative

### Query

| Endpoint | `/api/cooperative/users/remove` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| user_ids | _string_ | optional, ids of the user you want to add to the cooperative |

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
| user_ids | _string_ | optional, ids of the user you want to check the roles. By default, will get the ids of the currently connected user |
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
| user_ids | _string_ | optional, ids of the user you want to check the roles. By default, will get the ids of the currently connected user |
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
| local_uri | _string_ | local_uri of the picture |
| hasCertificate | _boolean(string)_ | return true if the user has completed all the formation |
| created_at | _datetime(string)_ | Date of addition of the formation in the database |
| updated_at | _datetime(string)_ | Date of modification of the formation in the database |

## Formations followed list

Get all formations followed by the user

### Query

| Endpoint | `/api/formations/followed` | Description |
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
| local_uri | _string_ | local_uri of the picture |
| hasCertificate | _boolean(string)_ | return true if the user has completed all the formation |
| created_at | _datetime(string)_ | Date of addition of the formation in the database |
| updated_at | _datetime(string)_ | Date of modification of the formation in the database |

## Search formations

Get all formations existings in the user's cooperatives matching the pattern given in body

### Query

| Endpoint | `/api/formations/search` | Description |
|----------|-------------|-------------|
|   pattern    |  _string_ |   required |
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
| local_uri | _string_ | local_uri of the picture |
| hasCertificate | _boolean(string)_ | return true if the user has completed all the formation |
| created_at | _datetime(string)_ | Date of addition of the formation in the database |
| updated_at | _datetime(string)_ | Date of modification of the formation in the database |

## Formation detail

Get all detail about a formation in particular

### Query

| Endpoint | `/api/formation` | Description |
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
| local_uri | _string_ | local_uri of the picture |
| hasCertificate | _boolean(string)_ | return true if the user has completed all the formation |
| created_at | _datetime(string)_ | Date of addition of the formation in the database |
| updated_at | _datetime(string)_ | Date of modification of the formation in the database |
| collaborators | _array(object)_ | List of authors |
| chapters | _array(object)_ | List of chapters that the formation contains |

Collaborators will be an array of objects of the following format:

| Key name | Value type | Description |
|----------|-------------|-------------|
|   id  | _int_ | id of the user    |
| first_name | _string_ | first name of user    |
| last_name | _string_ | last name of user    |

Chapters will be an array of objects of the following format:

| Key name | Value type | Description |
|----------|-------------|-------------|
| id | _int_ | chapter id |
| name | _string_ | chapter name |
| type | _string_ | chapter type (lesson, quizz or activity) |
| is_achieved | _boolean_ | return if the user doing the request have done this chapter |
| medias | _array(object)_ | List of media that the chapter contains |

Media will be an array of objects of the following format:

| Key name | Value type | Description |
|----------|-------------|-------------|
| id | _int_ | chapter id |
| name | _string_ | media name |
| type | _string_ | media type  |
| local_uri | _string_ | local uri of the media |
| size | _int_ | size of the media |

## Add formation in cooperative

The user adding the formation to a cooperative must be an "enseignant" member of this cooperative.

### Query

| Endpoint | `/api/formations/add` | Description |
|----------|-------------|-------------|
| name | _string_ | Formation name |
| estimated_duration | _int_ | Formation estimated_duration to finish |
| level | _int_ | Formation level of difficulty |
| cooperative_id | _int_ | Cooperative id of the formation |
|   main_pic    | _FILE_ | local_uri of the picture |


### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## Remove formation in cooperative

The user removing the formation to a cooperative must be an "enseignant" member of this cooperative.

### Query

| Endpoint | `/api/formations/remove` | Description |
|----------|-------------|-------------|
|   formation_id    |  _int_ |   id of the formation  |
|   cooperative_id    |  _int_ |   id of the cooperative  |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||



## Check if a formation is followed by the user

### Query

| Endpoint | `/api/formations/isFollowed` | Description |
|----------|-------------|-------------|
|   formation_id    |  _int_ |   id of the formation  |
|   cooperative_id    |  _int_ |   id of the cooperative  |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| is_followed | _boolean(string)_   |   "true" or "false"    |

## Follow a formation in cooperative

The user following the formation of a cooperative must be a member of this cooperative.

### Query

| Endpoint | `/api/formations/follow` | Description |
|----------|-------------|-------------|
|   formation_id    |  _int_ |   id of the formation  |
|   cooperative_id    |  _int_ |   id of the cooperative  |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

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

For commercials only.

### Query

| Endpoint | `/api/cooperative/tours/add` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| name | _string_ | can be empty, name of the tour to add |
| type | _string_ | 'gathering' or 'distribution' |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| tour_id | _int_ | identifier of the tour created |

## Cooperative tours remove

For commercials only.

### Query

| Endpoint | `/api/cooperative/tours/remove` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| tour_id | _int_ | identifier of the tour to remove |

## Cooperative tour schedules list

### Query

| Endpoint | `/api/cooperative/tour/schedules` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| tour_id | _int_ | identifier of the tour to list the schedules |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| from | _datetime(string)_ ||
| to | _datetime(string)_ ||
| place | _string_ | Place where the tour will operate. Generally, a postal address |

## Cooperative tour schedule add

For commercials only.

### Query

| Endpoint | `/api/cooperative/tour/schedules/add` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| tour_id | _int_ | identifier of the tour to list the schedules |
| from | _datetime_ | datetime of the start of the tour |
| to | _datetime_ | datetime of the stop of the tour |
| place | _string_ | Place where the tour will operate. Generally, a postal address |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| schedule_id | _int_ | identifier of the tour added |

## Cooperative tour schedule remove

For commercials only.

### Query

| Endpoint | `/api/cooperative/tour/schedules/remove` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| tour_id | _int_ | identifier of the tour of the schedule|
| schedule_id | _int_ | identifier of the schedule tour to remove |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## Cooperative items list

The cooperative buy and sell items for which it may be mandatory to validate a certification. This route lists the possible items that can be bought and sold.

### Query

| Endpoint | `/api/cooperative/items` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| offset | _int_ | default is 1 |
| interval | _int_ | default is 20 |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| item_id | _int_ ||
| name | _string_ ||
| formation_id | _int_ | nullable, identifier of the formation to acquiere to be able to sell the product |
| image | _string_ ||

## Cooperative item details

### Query

| Endpoint | `/api/cooperative/item` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| item_id | _int_ ||

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| name | _string_ ||
| description | _string_ ||
| created_at | _datetime(string)_ ||
| updated_at | _datetime(string)_ ||
| unit | _string_ | Unit on which the quentity is expressed |
| formation_id | _int_ | identifier of the formation to acquiere to be able to sell the product |
| formation_name | _string_ | nullable, name of the formation to acquiere to be able to sell the product |
| images | _array<string>_ | Images of the item |

## Cooperative item add

For commercials only.

### Query

| Endpoint | `/api/cooperative/items/add` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| formation_id | _string_ | nullable, identifier of the formation to acquiere to be able to sell the product |
| name | _string_ ||
| description | _string_ ||
| unit | _string_ | unit with which the quantities will be expressed (can be g,mg,kg,t,L,mL) |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| item_id | _int_ | identifier of the item added |

## Cooperative item remove

For commercials only.

### Query

| Endpoint | `/api/cooperative/items/remove` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| formation_id | _string_ | nullable, identifier of the formation to acquiere to be able to sell the product |
| item_id | _int_ | identifier of the item to remove |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## Cooperative item add image

For commercials only.

### Query

| Endpoint | `/api/cooperative/item/add_image` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| item_id | _int_ | identifier of the item added |
| image | _file_ | PNG, GIF, JPEG or JPG only |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| image_id | _int_ | identifier of the image |
| image | _string_ | uri to the image |

## Cooperative item remove image

For commercials only.

### Query

| Endpoint | `/api/cooperative/item/remove_image` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| item_id | _int_ | identifier of the item added |
| image_id | _int_ | identifier of the image |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## Cooperative inventory list

### Query

| Endpoint | `/api/cooperative/inventory` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| user_item_id | _int_ ||
| item_id | _int_ ||
| name | _string_ ||
| description | _string_ ||
| unit | _string_ ||
| formation_id | _int_ | nullable, identifier of the formation to acquiere to be able to sell the product |
| image | _string_ | uri |
| quantity | _int_ | quantity of objects to sell |
| price | _float(10,2)_ | price for 1 item |

## Cooperative inventory add

For commercials only.
So users can buy the products.

### Query

| Endpoint | `/api/cooperative/inventory/add` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| item_id | _int_ | identifier of the item added |
| quantity | _int_ | identifier of the image |
| price | _float_ | price for 1 item |
| message | _float_ | custom message for the item |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| user_item_id | _int_ ||

## Cooperative inventory remove

For commercials only.

### Query

| Endpoint | `/api/cooperative/inventory/remove` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| user_item_id | _int_ ||

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## User inventory in cooperative

### Query

| Endpoint | `/api/account/inventory` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| user_ids | _string_ | optional, ids of the user you want to get the inventory. By default, will get the ids of the currently connected user |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| user_item_id | _int_ ||
| item_id | _int_ ||
| name | _string_ ||
| description | _string_ ||
| unit | _string_ ||
| message | _string_ ||
| formation_id | _int_ | nullable, identifier of the formation to acquiere to be able to sell the product |
| image | _string_ ||
| quantity | _int_ | quantity of objects to sell |
| price | _float(10,2)_ | price for 1 item |

## User inventory add

So the cooperative can buy the products.

### Query

| Endpoint | `/api/account/inventory/add` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| item_id | _int_ | identifier of the item added |
| quantity | _int_ | identifier of the image |
| price | _float_ | price for 1 item |
| message | _float_ | price for 1 item |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| user_item_id | _int_ ||

## User inventory remove

For commercials only.
So users can buy the products.

### Query

| Endpoint | `/api/account/inventory/remove` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| user_item_id | _int_ ||

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## Items sold by all users in a cooperative

For commercials.
For the cooperative to buy the items of user.

### Query

| Endpoint | `/api/cooperative/users/items` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| user_item_id | _int_ ||
| user_ids | _string_ ||
| item_id | _int_ ||
| name | _string_ ||
| message | _string_ ||
| formation_id | _int_ | nullable, identifier of the formation to acquiere to be able to sell the product |
| image | _string_ ||
| quantity | _int_ | quantity of objects to sell |
| price | _float(10,2)_ | price for 1 item |

=======

## Cooperative orders

For commercials.

### Query

| Endpoint | `/api/cooperative/orders` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| order_id | _int_ ||
| buyer_ids| _string_ ||
| buyer_username | _string_ ||
| from | _datetime(string)_ ||
| to | _datetime(string)_ ||
| place | _string_ | Place where the tour will operate. Generally, a postal address |

## Order items list

### Query

| Endpoint | `/api/cooperative/order/items` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| name | _string_ ||
| description | _string_ ||
| created_at | _datetime(string)_ ||
| updated_at | _datetime(string)_ ||
| unit | _string_ | Unit on which the quentity is expressed |
| formation_id | _int_ | identifier of the formation to acquiere to be able to sell the product |
| formation_name | _string_ | nullable, name of the formation to acquiere to be able to sell the product |
| images | _array<string>_ | Images of the item |

## Cooperative approve order

For commercials.
If the item has successfuly been received by the cooperative.
Or if the item has successfuly been given to the user.

### Query

| Endpoint | `/api/cooperative/order/approve` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| order_id | _int_ ||

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## Cooperative desapprove order

For commercials.

### Query

| Endpoint | `/api/cooperative/order/desapprove` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| order_id | _int_ ||

### Response

| Key name | Value type | Description |
|----------|-------------|-------------|
| _No data_ |||

## User orders in cooperative

### Query

| Endpoint | `/api/account/orders` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| user_ids | _string_ | optional, ids of the user you want to check the roles. By default, will get the ids of the currently connected user |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| order_id | _int_ ||
| tour_id | _int_ ||
| username | _string_ ||
| type | _string_ | 'buy' if the user buys the item to the cooperative, 'sell' if the user sells the item to the cooperative |
| from | _datetime(string)_ ||
| to | _datetime(string)_ ||
| place | _string_ | Place where the tour will operate. Generally, a postal address |

## Buy - Place order

User buys to cooperative.

### Query

| Endpoint | `/api/cooperative/buy` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| schedule_id | _int_ | id of the cooperative |
| tour_id | _int_ | id of the cooperative |
| items_id | _array<int>_ | Identifiers list of "user_item" to get |
| quantities | _array<int>_ | List of "user_item" quantities to get (length must match the number of items_id) |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| order_id | _int_ ||

## Place sell

User sells to cooperative.
This route verifies if the user has the formation certificate to sell its products.

### Query

| Endpoint | `/api/cooperative/sell` | Description |
|----------|-------------|-------------|
| cooperative_id | _int_ | id of the cooperative |
| schedule_id | _int_ | id of the cooperative |
| tour_id | _int_ | id of the cooperative |
| items_id | _array<int>_ | Identifiers list of "user_item" to sell |
| quantities | _array<int>_ | List of "user_item" quantities to sell (length must match the number of items_id) |

### Response

The response will be an array of objects of the following format :

| Key name | Value type | Description |
|----------|-------------|-------------|
| order_id | _int_ ||