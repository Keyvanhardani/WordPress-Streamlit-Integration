# WordPress-Streamlit Integration

## Description

This project enables WordPress user authentication in Streamlit applications. With this plugin and a bit of code, you can set up your Streamlit server to accept JWT (JSON Web Tokens) from a WordPress server. This way, you can integrate a login system into your Streamlit web application.

JWTs are digital signatures used to verify the authenticity and integrity of data. They consist of a header, payload, and signature and are commonly used for authentication in web applications.

## Installation

- Ensure that the "JWT Authentication for WP REST API" plugin is installed, as it is required for this integration. [JWT Authentication for WP REST API Plugin](https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/) (version 1.3.2 or higher)

- Download the WordPress plugin from this GitHub page and install it into your WordPress instance.

- Activate the plugin.

- Navigate to the plugin settings page (Streamlit Plugin in the WordPress dashboard).

- Set your API key in the settings page. Remember to keep this key secret as it's used for communication between your Streamlit application and the WordPress server. The API key is entered in the "API Key" field.

## Streamlit Code Integration

Copy the following Python code into your Streamlit application, replacing the URL and API_KEY with your own values:
```
python
import requests
import streamlit as st

API_KEY = 'YOUR_API_KEY'  # Replace this with your API key

st.set_page_config(layout="wide")

def get_token(username, password):
    # Sends a POST request to the WordPress REST API to obtain a JWT
    response = requests.post(
        'https://yourwordpressurl.com/wp-json/jwt-auth/v1/token',  # Replace this with the URL of your WordPress installation
        data={'username': username, 'password': password},
        headers={'X-API-KEY': API_KEY}
    )
    if response.status_code == 200:
        return response.json()['token']
    else:
        return None

def verify_token(token):
    # Sends a POST request to the WordPress REST API to validate the JWT
    response = requests.post(
        'https://yourwordpressurl.com/wp-json/jwt-auth/v1/token/validate',  # Replace this with the URL of your WordPress installation
        headers={'Authorization': f'Bearer {token}', 'X-API-KEY': API_KEY}
    )
    return response.status_code == 200

def main():
    st.write("This is the main page of the application.")  # Your main code goes here

# Check if the user is already logged in
if 'token' in st.session_state and verify_token(st.session_state['token']):
    main()  # Call the main function
else:
    # Show the login form
    col1, col2, col3 = st.columns([1, 1, 1])
    with col1:
        st.write("")
    with col2:
        with st.form(key='login_form'):
            st.title("Please log in")
            username = st.text_input('Username')
            password = st.text_input('Password', type='password')
            submit_button = st.form_submit_button(label='Log in')
            if submit_button:
                token = get_token(username, password)
                if token and verify_token(token):
                    st.session_state['token'] = token  # Store the token in the session state
                    st.experimental_rerun()  # Reload the page so that the login form disappears
                else:
                    st.error('Access denied')
    with col3:
        st.write("")
```

Adapt the code to your specific requirements, such as replacing the URL and API key with your own values.

## Usage

Once the plugin is set up and the code is integrated, the Streamlit application will present a login form to any user who is not logged in. The user can log in with their WordPress username and password. If the login is successful, the Streamlit application will run the `main()` function, which can be customized as needed.

This system will also maintain the login state across reloads, as long as the JWT has not expired. The expiration time of the JWT can be set in the WordPress plugin.

## Security Considerations

- Ensure that you keep the API key secret and do not store it in public code or version control systems.
- Always use HTTPS to encrypt the communication between the Streamlit application and the WordPress server.
- Carefully validate and sanitize all inputs and outputs to avoid potential security vulnerabilities.

## Troubleshooting

- If the login is not working, check the network requests in the browser's developer tools to determine if the API requests are successful and what error messages are being returned.
- Verify that the API key is correctly entered in the WordPress plugin settings and in the Streamlit code.
- Check if the JWT Authentication for WP REST API plugin is activated in WordPress and functioning properly.

## Contributing

We welcome contributions to this project! If you would like to make improvements or add new features, please create a pull request on GitHub. Ensure that your changes adhere to the coding guidelines and have been thoroughly tested.

If you find any bugs or have suggestions, please open an issue on the project's GitHub page.
