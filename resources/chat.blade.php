<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Realtime - Minggu 11 - BKPM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: sans-serif; padding: 30px; background: #f3f4f6; }
        #messages { background: white; border: 1px solid #ddd; height: 300px; overflow-y: auto; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .bubble { background: #dbeafe; padding: 8px 12px; border-radius: 12px; margin-bottom: 8px; display: inline-block; }
    </style>
</head>
<body>
    <h1 class="text-2xl font-bold mb-2">Minggu 11: Chat Realtime dengan Laravel Reverb</h1>
    <p class="text-sm text-gray-500 mb-4">Nama: Dian - Broadcast Channel: chat</p>

    <div id="messages">
        <div class="text-gray-400 text-sm">Belum ada pesan... Ketik di bawah untuk mulai</div>
    </div>

    <div class="flex gap-2">
        <input type="text" id="messageInput" class="border p-2 rounded w-full" placeholder="Ketik pesan disini...">
        <button id="sendBtn" class="bg-blue-600 text-white px-6 py-2 rounded font-bold">Kirim</button>
    </div>

    <script>
        // Fungsi kirim pesan ke server
        document.getElementById('sendBtn').addEventListener('click', function() {
            sendMessage();
        });
        
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') sendMessage();
        });

        function sendMessage() {
            let input = document.getElementById('messageInput');
            let msg = input.value;
            if(msg.trim() === '') return;

            fetch('/chat/kirim', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({message: msg})
            });

            input.value = '';
        }

        // Fungsi dengerin broadcast dari Reverb
        window.addEventListener('DOMContentLoaded', () => {
            if(window.Echo) {
                window.Echo.channel('chat')
                    .listen('.message.sent', (e) => {
                        addMessage(e.message);
                    })
                    .listen('MessageSent', (e) => {
                        addMessage(e.message);
                    });
            }
        });

        function addMessage(message) {
            let div = document.getElementById('messages');
            // hapus tulisan awal kalau ada
            if(div.innerHTML.includes('Belum ada pesan')) div.innerHTML = '';
            
            div.innerHTML += `<div><span class="bubble">${message}</span></div>`;
            div.scrollTop = div.scrollHeight;
        }
    </script>
</body>
</html>