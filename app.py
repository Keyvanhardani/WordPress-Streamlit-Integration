import requests
import streamlit as st

API_KEY = 'YOUR_API_KEY'  # Set your API key here

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
    st.write("This is the main page of the application.")  # Your main code goes here

# Check if the user is already logged in
if 'token' in st.session_state and verify_token(st.session_state['token']):
    main()  # Call the main function
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
