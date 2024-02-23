source .env

NGROK_HOSTNAME=$(curl --silent --show-error http://127.0.0.1:4040/api/tunnels | sed -nE 's/.*public_url":"https:..([^"]*).*/\1/p')

echo "https://$NGROK_HOSTNAME"

curl "https://api.telegram.org/bot${TG_MALL_BOT_TOKEN}/setWebhook?url=${NGROK_HOSTNAME}/layerok/tgmall/webhook"
