server {
    listen 8080;
    server_name 120.76.26.101;

    root /home/frontend/html;
    index index.html index.htm;

    location / {
        # First attempt to serve request as file, then
        # as directory, then fall back to displaying a 404.
        try_files $uri $uri/ =404;
        # Uncomment to enable naxsi on this location
        # include /etc/nginx/naxsi.rules
    }
}