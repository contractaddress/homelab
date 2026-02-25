# Easy Caddy setup

#### *On Alpine:*

```
apk update
```

```
apk add caddy
```

Add your caddy config

```
vim /etc/caddy/Caddyfile
```

validate the config

```
caddy validate --config /etc/caddy/Caddyfile
```

add it as a service and start it

```
rc-update add caddy && rc-service caddy start
```

---

#### *For Debian:*

[See docs](https://caddyserver.com/docs/install#debian-ubuntu-raspbian)

## Caddy basic authentication

if your service doesn't provide authentication you can utilise caddy's basicauth and `caddy hash-password` (cli)

```yml
kiwix.homelab.lan {
    tls internal
    reverse_proxy {LXC-IP}:8081 
    basicauth {
        $username $HashedCaddyPWD 
        }
```

here's how to use it in your Caddyfile, replace the username and hash with what you got from `hash-password`
