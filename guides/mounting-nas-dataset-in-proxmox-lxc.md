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

