source .env

ngrok http $APP_URL --host-header=rewrite
