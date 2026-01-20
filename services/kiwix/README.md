## Kiwix installation

```
wget https://download.kiwix.org/release/kiwix-tools/kiwix-tools_linux-x86_64.tar.gz
```

```
tar -xf kiwix-tools_linux-x86_64.tar.gz
```

```
mv kiwix-tools_linux-x86_64 kiwix && cd kiwix
```

> [!NOTE] Change Path to where your Zims are located

```
./kiwix-serve --address=all  --port=8080 /mnt/kiwix/*.zim
```
