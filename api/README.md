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

## List of cooperatives

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