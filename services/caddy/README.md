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
