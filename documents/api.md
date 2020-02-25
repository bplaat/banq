# The Banq API
The Banq website has also a web API, to use this API you need an API key which you can create via the devices admin page.

## Paged routes
All the routes where you get a list of data are paged. This means that the data is split up in to pages that you need to fetch separately. You can change the paging process by the folling parameters:
- `page` The number of the page you want to see, one based
- `limit` The limit of items per page, minimal 1 and maximal 50

---

## Devices

### /api/devices
Get some information about all devices (paged)

### /api/devices/search
Search for devices (paged)
- `q` search query

### /api/devices/{device_id}
Get information about a single device

### /api/devices/create
Create a new device
- `name` The device name

### /api/devices/{device_id}/edit
Edit a device with new information
- `name` The device name

### /api/devices/{device_id}/delete
Delete a device

---

## Users

### /api/users
Get some information about all users (paged)

### /api/users/search
Search for users (paged)
- `q` search query

### /api/users/{user_id}
Get information about a single user

### /api/users/create
Create a new user
- `firstname` The users firstname
- `lastname` The users lastname
- `username` The users username
- `email` The users email
- `password` The users password

### /api/users/{user_id}/edit
Edit a user with new information
- `firstname` The users firstname
- `lastname` The users lastname
- `username` The users username
- `email` The users email
- `password` The users password

### /api/users/{user_id}/delete
Delete a user

---

## Accounts

### /api/accounts
Get some information about all accounts (paged)

### /api/accounts/search
Search for accounts (paged)
- `q` search query

### /api/accounts/{account_id}
Get information about a single account

### /api/accounts/create
Create a new account
- `name` The account name
- `type` The account type
- `user_id` The account user id
- `amount` The account amount

### /api/accounts/{account_id}/edit
Edit a account with new information
- `name` The account name
- `type` The account type
- `user_id` The account user id
- `amount` The account amount

### /api/accounts/{account_id}/delete
Delete a account

---

## Transactions

### /api/transactions
Get some information about all transactions (paged)

### /api/transactions/search
Search for transactions (paged)
- `q` search query

### /api/transactions/{transaction_id}
Get information about a single transaction

### /api/transactions/create
Create a new transaction
- `name` The transaction name
- `from_account_id` The transaction from account id
- `to_account_id` The transaction to account id
- `amount` The transaction amount

---

## Payment Links

### /api/payment-links
Get some information about all payment links (paged)

### /api/payment-links/search
Search for payment links (paged)
- `q` search query

### /api/payment-links/{link}
Get information about a single payment link

### /api/payment-links/create
Create a new payment link
- `name` The payment link name
- `account_id` The payment link account id
- `amount` The payment link amount
