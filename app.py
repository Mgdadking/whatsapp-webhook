from flask import Flask, request
import os

app = Flask(__name__)

LINKS_FILE = 'links.txt'
COUNTER_FILE = 'counter.txt'

def load_links():
    with open(LINKS_FILE, 'r') as f:
        return [line.strip() for line in f.readlines() if line.strip()]

def get_next_link():
    links = load_links()

    if not os.path.exists(COUNTER_FILE):
        with open(COUNTER_FILE, 'w') as f:
            f.write('0')

    with open(COUNTER_FILE, 'r') as f:
        counter = int(f.read().strip())

    link = links[counter % len(links)]

    with open(COUNTER_FILE, 'w') as f:
        f.write(str(counter + 1))

    return link

@app.route('/webhook', methods=['POST'])
def webhook():
    data = request.json
    if not data or 'messages' not in data:
        return "No message", 400

    sender = data['messages'][0]['from']
    link = get_next_link()

    return {
        "messages": [
            {
                "to": sender,
                "type": "text",
                "text": { "body": f"مرحباً! للتقديم يرجى الضغط على الرابط التالي:\n{link}" }
            }
        ]
    }

if __name__ == '__main__':
    app.run()