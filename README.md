# My Homelab

Welcome to my Homelab repo! Here you can find my Docker composes & Guides for self-hosted related things.

# Table of Contents

- [Docker composes, configs & more](services/)
- [Mounting a NAS dataset in a Proxmox LXC](#mounting-a-nas-dataset-in-a-proxmox-lxc)
- [Local Domains](#Reverse-proxy,-HTTPS-and-local-domains)
- (MORE SOON)

# **Services in my Homelab**

#### *Proxmox VE*

- [**Gitea**](services/gitea) - local github
- [**Nextcloud**](services/nextcloud) - local cloud
- [**Immich**](services/immich) - photo/video storage
- [**Kiwix**](services/kiwix) - offline browser
- [**Komga**](services/komga) - manga reading
- [**Suwayomi**](services/suwayomi) - manga downloading
- [**Pihole**](services/pihole) -  Local Domains, Adblock
- [**Authentik**](services/authentik) - homelab authentication
- [**Caddy**](services/caddy) - reverse proxy
- [**Qbittorrent & Gluetun**](services/) - Torrenting
- [**Cyberchef**](services/cyberchef) - digital swiss army knife
- [**Ollama**](services/) - Mistral 7B (local LLM)

> [!NOTE] Most of these services are ran in an LXC (mostly Alpine my goat)

#### *OpenWrt* - *Router, Network isolation*

#### *TrueNAS* - *Network attached storage*

- 2x4TB - RAID1
- 1x3TB - STRIP
- 1x1TB - STRIP

# **Hardware**

- Thinkcentre 920q (i5-8500T, 32GB DDR4)
- Acer Veriton N4660G (i5-8500T, 8GB DDR4)
- Raspberry pi 4 (8GB)
- ASUS TUF-AX6000
- Netgear Gs108ge
- DeskPi 7.84" touch screen
- 2x4TB WD red HDD
- 1x3TB WD red HDD
- 1x1TB Seagate HDD

---

# Mounting a NAS dataset in a Proxmox LXC

for this example we will be using an SMB dataset we created in TrueNas named "Manga"  

now the SMB share exists as `//{NAS_IP}/Manga`

We will be using **Proxmox** as a Middle man between the SMB dataset and the LXC.  
So lets start by creating a NAS directory in /mnt in our Proxmox VE.

```
mkdir -p /mnt/nas/manga
```

we can create a new directory for every dataset we make for our NAS for ease of mounting

### ==*LXC group*==

inside the target LXC make a new group with `GID 10000` so we don't face permission issues

```
groupadd -g 10000 LXC_share
```

`addgroup` on alpine

```
mkdir -p /mnt/manga
```

### ==*Mounting the SMB dataset to Proxmox host*==

Now back in the proxmox VE shell lets edit the fstab (NOT in the LXC)

```
vim /etc/fstab
```

add this line at the bottom to connect to and mount the NAS dataset onto our proxmox host

```
//{NAS_IP}/Manga /mnt/nas/manga cifs _netdev,x-systemd.automount,noatime,uid=100000,gid=110000,dir_mode=0770,file_mode=0770,user={USER},pass={PASSWORD} 0 0
```

*replace {NAS_IP} with your smb server*

### ==Mounting the SMB dataset in the LXC==

replace number with your LXC id

```
vim /etc/pve/lxc/101.conf
```

add this at the end of the file

```
mp0: /mnt/nas/manga,mp=/mnt/manga,acl=1
```

### ==*Done! now the NAS SMB dataset is available to us inside the LXC*==

```
ls -lha /mnt/
total 8.0K
drwxr-xr-x  3 root root  4.0K Oct 22 01:20 .
drwxr-xr-x 18 root root  4.0K Jan 12 01:08 ..
drwxrwx---  2 root 10000    0 Jan 13 00:45 manga
```

you can now bind it to a volume in a docker compose for example or use it for any other purpose!

---

# Reverse proxy, HTTPS and local domains

*There seems to be a lack of guides on local domains especially when using OpenWrt so hopefully this helps...*
I'm using OpenWrt as secondary NAT router to isolate the homelab from the rest of my network.

![Alt text](assets/localDNS.excalidraw.png)

LAN client → service.homelab.lan → Pi-hole resolves to Caddy IP → client connects to Caddy → Caddy makes a secure connection to the LXC and forwards the response to the client

### First set up Pi-hole in your LAN

Pi-hole is a great network-wide adblocker but many people neglect it's ability to also act as a local DNS server. You can use my [docker compose](services/pihole).

We'll come back to Pi-hole later to make some changes in the web UI

### Advertise Openwrt clients to use Pi-hole as a DNS server

i faced issues while trying to do this through the web UI so i recommend SSH'ing into your openwrt

```sh
ssh {OPENWRT_IP}
```

open `/etc/dnsmasq.conf` file and add this line at the end:

```sh
dhcp-option=6,{PI-HOLE IP}
```

instantly apply changes

```sh
/etc/init.d/dnsmasq restart
```

All clients connected to OpenWrt will use Pi-hole for DNS, so the native ad-blocking works immediately, even before configuring Caddy or local domains.

You may have issues with windows devices, so manually set the DNS server to pihole if necessary.

### Caddy for the reverse proxy and HTTPS

You can find a quick guide on setting up Caddy for alpine [here](services/caddy)

for this guide as we are in a trusted environment so TLS internal is good enough.

```yml
# example of a reverse proxy in your caddy file 

service.homelab.lan {
    tls internal
    reverse_proxy {LXC-IP}:8080 # LXC ip refers to the ip of where the service is running
```

### Final steps in Pi-hole's WebUI

Access Pi-hole and configure upstream DNS to openwrt (or another server if you prefer)

![pihole1](assets/pihole1.png)

head to **settings -> Local DNS record**. Add a DNS record with your domain of choice and make it point to Caddy's IP.

Now for each service you can add a CNAME as shown in the screenshot below

![pihole2](assets/pihole2.png)
tips:

- chose a TLD that cannot exist: `.lan` `.home` `.local` `.lab`
- install the Caddy root certificate and install it on the browser you use to browse your homelab

#### Done! now we can access <https://service.homelab.lan> in our browser
