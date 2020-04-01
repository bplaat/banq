# The Banq API
The Banq website has also a web API

## Authentication
To use this API you need an API key which you can create via the devices admin page. And you need to authenticate as a user by using a user session. If you authenticated with an admin account you get more privilages (via the `admin/` prefix) then with an normal account.
- `key` The API device key
- `session` The user session

## Paged routes
All the routes where you get a list of data are paged. This means that the data is split up in to pages that you need to fetch separately. You can change the paging process by the folling parameters:
- `page` The number of the page you want to see, one based
- `limit` The limit of items per page, minimal 1 and maximal 50

---

## ATM Accounts
*The following routes don't need authentication via an user session*

### /api/atm/accounts/{account_id}
Get information about an account via a rfid card and pincode
- `rfid` The rfid card matching the account id
- `pincode` the pincode matching the card

---

## ATM Transactions
*The following routes don't need authentication via an user session*

### /api/atm/transactions/create
Create a new transaction
- `rfid` The rfid card matching the account id
- `pincode` the pincode matching the card
- `name` The transaction name
- `from_account_id` The from account id form the card
- `to_account_id` The transaction to account id
- `amount` The transaction amount

---

## Auth
*The following routes don't need authentication via an user session*

### /api/auth/login
Login with someones username or email and password and returns an user session
- `login` The users username or email
- `password` The users password

## /api/auth/register
Create a new normal user and returns an user session
- `firstname` The users firstname
- `lastname` The users lastname
- `username` The users username
- `email` The users email
- `password` The users password
- `phone_number` The users phone number
- `sex` The users gender
    - **M**: The user is male
    - **F**: The user is female
    - **X**: The user is of another gender or it is complicated
- `birth_date` The users birth date
- `address` The users address
- `postcode` The users post code
- `city` The city the user lives in
- `region` The region the user lives in

---

## Auth

## /api/auth/logout
Expire the session of the authed user

## /api/auth/edit_details
Edit the details of the authed user
- `firstname` The users firstname
- `lastname` The users lastname
- `username` The users username
- `email` The users email
- `phone_number` The users phone number
- `sex` The users gender
    - **M**: The user is male
    - **F**: The user is female
    - **X**: The user is of another gender or it is complicated
- `birth_date` The users birth date
- `address` The users address
- `postcode` The users post code
- `city` The city the user lives in
- `region` The region the user lives in

## /api/auth/edit_password
Edit the password of the authed user
- `old_password` The users old password
- `passsword` The users new password

## /api/auth/delete
Deleted the authed user

---

## Sessions

## /api/sessions
Get all the authed user sessions

## /api/sessions/{session_id}/revoke
Revoke an authed user session

---

## Accounts

### /api/accounts
Get some information about all your accounts (paged)

### /api/accounts/search
Search for one of your accounts (paged)
- `q` search query

### /api/accounts/create
Create a new account
- `name` The account name
- `type` The account type

### /api/accounts/{account_id}
Get information about one of your accounts

### /api/accounts/{account_id}/edit
Edit one of your accounts with new information
- `name` The account name
- `type` The account type

### /api/accounts/{account_id}/delete
Delete one of your accounts

---

## Transactions

### /api/transactions
Get some information about all your transactions (paged)

### /api/transactions/search
Search for one of your transactions (paged)
- `q` search query

### /api/transactions/create
Create a new transaction
- `name` The transaction name
- `from_account_id` The transaction from account id
- `to_account_id` The transaction to account id
- `amount` The transaction amount

### /api/transactions/{transaction_id}
Get information about one of your accounts

---

## Payment Links

### /api/payment-links
Get some information about all your payment links (paged)

### /api/payment-links/search
Search for one of your payment links (paged)
- `q` search query

### /api/payment-links/create
Create a new payment link
- `name` The payment link name
- `account_id` The payment link account id
- `amount` The payment link amount

### /api/payment-links/{payment_link_id}
Get information about one of your payment links

### /api/payment-links/{payment_link_id}/delete
Delete one of your payment links

---

## Cards

### /api/cards
Get some information about all your cards (paged)

### /api/cards/search
Search for one of your cards (paged)
- `q` search query

### /api/cards/create
Create a new card
- `name` The payment link name
- `account_id` The payment link account id
- `rfid` The card rfid (4 hex bytes)
- `pincode` The card pincode (4 digits)

### /api/cards/{card_id}
Get information about one of cards

### /api/cards/{card_id}/delete
Delete one of your cards

---

## Admin Devices

### /api/admin/devices
Get some information about all devices (paged)

### /api/admin/devices/search
Search for devices (paged)
- `q` search query

### /api/admin/devices/create
Create a new device
- `name` The device name

### /api/admin/devices/{device_id}
Get information about a single device

### /api/admin/devices/{device_id}/edit
Edit a device with new information
- `name` The device name

### /api/admin/devices/{device_id}/delete
Delete a device

---

## Admin Users

### /api/admin/users
Get some information about all users (paged)

### /api/admin/users/search
Search for users (paged)
- `q` search query

### /api/admin/users/create
Create a new user
- `firstname` The users firstname
- `lastname` The users lastname
- `username` The users username
- `email` The users email
- `password` The users password
- `phone_number` The users phone number
- `sex` The users gender
    - **M**: The user is male
    - **F**: The user is female
    - **X**: The user is of another gender or it is complicated
- `birth_date` The users birth date
- `address` The users address
- `postcode` The users post code
- `city` The city the user lives in
- `region` The region the user lives in
- `role` The role the user has
    - **1**: A normal user
    - **2**: A admin user

### /api/admin/users/{user_id}
Get information about a single user

### /api/admin/users/{user_id}/edit
Edit a user with new information
- `firstname` The users firstname
- `lastname` The users lastname
- `username` The users username
- `email` The users email
- `password` The users password (*optional*)
- `phone_number` The users phone number
- `sex` The users gender
    - **M**: The user is male
    - **F**: The user is female
    - **X**: The user is of another gender or it is complicated
- `birth_date` The users birth date
- `address` The users address
- `postcode` The users post code
- `city` The city the user lives in
- `region` The region the user lives in
- `role` The role the user has
    - **1**: A normal user
    - **2**: A admin user

### /api/admin/users/{user_id}/delete
Delete a user

---

## Admin Accounts

### /api/admin/accounts
Get some information about all accounts (paged)

### /api/admin/accounts/search
Search for an account (paged)
- `q` search query

### /api/admin/accounts/create
Create a new account
- `name` The account name
- `type` The account type
- `user_id` The account user id
- `amount` The account amount

### /api/admin/accounts/{account_id}
Get information about an account

### /api/admin/accounts/{account_id}/edit
Edit an account with new information
- `name` The account name
- `type` The account type
- `user_id` The account user id
- `amount` The account amount

### /api/admin/accounts/{account_id}/delete
Delete an account

---

## Admin Transactions

### /api/admin/transactions
Get some information about all transactions (paged)

### /api/admin/transactions/search
Search for a transaction (paged)
- `q` search query

### /api/admin/transactions/create
Create a new transaction
- `name` The transaction name
- `from_account_id` The transaction from account id
- `to_account_id` The transaction to account id
- `amount` The transaction amount

### /api/admin/transactions/{transaction_id}
Get information a transaction

---

## Admin Payment Links

### /api/admin/payment-links
Get some information about all payment links (paged)

### /api/admin/payment-links/search
Search for a payment link (paged)
- `q` search query

### /api/admin/payment-links/create
Create a new payment link
- `name` The payment link name
- `account_id` The payment link account id
- `amount` The payment link amount

### /api/admin/payment-links/{payment_link_id}
Get information about a payment link

### /api/admin/payment-links/{payment_link_id}/delete
Delete a payment link

---

## Admin Cards

### /api/admin/cards
Get some information about all cards (paged)

### /api/admin/cards/search
Search for a card (paged)
- `q` search query

### /api/admin/cards/create
Create a new card
- `name` The payment link name
- `account_id` The payment link account id
- `rfid` The card rfid (4 hex bytes)
- `pincode` The card pincode (4 digits)

### /api/admin/cards/{card_id}
Get information about a card

### /api/admin/cards/{card_id}/delete
Delete a card

---

## Admin Sessions

## /api/admin/sessions
Get all the users sessions

## /api/admin/sessions/{session_id}/revoke
Revoke an user session
