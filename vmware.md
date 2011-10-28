VMWare
======

* Mac host, Windows vm network set up:
  1. Set Virtual Machine > Network Adapter to NAT
  2. Get host machine IP via ifconfig (under en0)
  3. In windows, edit C:\\Windows\\System32\\drivers\\etc\\hosts to point _localhost_ at the host machine IP
* How to get the ip address of the vm:
  1. launch vmware
  2. launch os in vmware
  3. run `cmd` (windows) to get terminal
  4. run `ipconfig` to get the ip address of the machine