=== Plugin Name ===
Contributors: Keyvan Hardani
Tags: streamlit, authentication, jwt, WordPress, Python
Requires at least: 4.7
Tested up to: 5.8
Stable tag: 1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This project enables WordPress users authentication in Streamlit applications. With this plugin and a bit of code, you can set up your Streamlit server to accept JWT (JSON Web Tokens) from a WordPress server. This way, you can integrate a login system into your Streamlit web application.

== Installation ==

Make sure to install the JWT Authentication for WP REST API plugin, as it is required for this integration.

Download the WordPress plugin from this GitHub page and install it into your WordPress instance.

Activate the plugin.

Navigate to the plugin settings page (Streamlit Plugin in the WordPress dashboard).

Set your API key in the settings page. Remember to keep this key secret as it's used for communication between your Streamlit application and the WordPress server.

== Streamlit Code Integration ==

Copy the following Python code into your Streamlit application, replacing the URL and API_KEY with your own.

import requests
import streamlit as st

API_KEY = 'YOUR_API_KEY' # Set your API key here

st.set_page_config(layout="wide")

def get_token(username, password):
response = requests.post(
'https://yourwordpressurl.com/wp-json/jwt-auth/v1/token',
data={'username': username, 'password': password},
headers={'X-API-KEY': API_KEY}
)
if response.status_code == 200:
return response.json()['token']
else:
return None

def verify_token(token):
response = requests.post(
'https://yourwordpressurl.com/wp-json/jwt-auth/v1/token/validate',
headers={'Authorization': f'Bearer {token}', 'X-API-KEY': API_KEY}
)
return response.status_code == 200

def main():
st.write("This is the main page of the application.") # Your main code goes here

Check if the user is already logged in
if 'token' in st.session_state and verify_token(st.session_state['token']):
main() # Call the main function
else:
# Show the login form
col1, col2, col3 = st.columns([1,1,1])
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
                st.session_state['token'] = token  # We store the token in the session state
                st.experimental_rerun()  # Reload the page so that the login form disappears
            else:
                st.error('Access denied')

with col3:
    st.write("")

== Usage ==

Once the plugin is set up and the code integrated, the Streamlit application will present a login form to any user who is not logged in. The user can log in with their WordPress username and password. If the login is successful, the Streamlit application will run the main() function, which can be customized as needed.

This system will also maintain the login state across reloads, as long as the JWT has not expired. The expiration time of the JWT can be set in the WordPress plugin.