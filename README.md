# Sockets Documentation

## What is a Socket?
A socket is a way for programs to communicate with other programs using standard Unix file descriptors.

- Sockets can work with `read()` and `write()`, but using `send()` & `recv()` is better and offers more control.
- We work with "internet sockets," which are divided into two types:

### Stream Sockets (SOCK_STREAM)
A two-way connected communication stream.

- **Used by:**
  - Telnet, SSH: Ensures all characters arrive in order.
  - Web browsers (HTTP) use this type of socket.
- **Ensures:**
  - High-quality data transmission using **TCP (Transmission Control Protocol)**.
  - **TCP** ensures sequential and error-free data arrival and uses **IP for routing (TCP/IP).**
- **Use case:** When accuracy is required.

### Datagram Sockets (SOCK_DGRAM)
A connectionless protocol using **UDP (User Datagram Protocol).**

- **Characteristics:**
  - Data may arrive out of order or not at all.
  - If data arrives, it is error-free.
- **Advantages:**
  - No need to maintain an open connection.
  - Faster as it doesn't require establishing a connection.
- **Used by:**
  - TFTP, DHCP clients.
  - Multiplayer games, streaming audio/video, video conferencing.
- **Use case:** When speed is more important than accuracy.

## Data Encapsulation
```
[ Ethernet [ IP [ UDP [ TFTP [ DATA ] ] ] ] ]
```
### Data Flow
- **Sending:**
  1. Data is created.
  2. Encapsulated into the **TFTP** protocol.
  3. Further encapsulated by **UDP** (Transport Layer by Kernel).
  4. **IP** adds routing information (Internet Layer by Kernel).
  5. Finally, **Ethernet** encapsulation (Physical Layer by hardware).
- **Receiving:**
  1. Ethernet layer strips its header.
  2. Kernel strips IP and UDP headers.
  3. TFTP program removes TFTP header.
  4. Data is obtained.

## Network Layers
1. **Application Layer** (e.g., Telnet, FTP).
2. **Host-to-Host Transport Layer** (e.g., TCP, UDP).
3. **Internet Layer** (e.g., IP and routing).
4. **Network Access Layer** (e.g., Ethernet, WiFi).

## IP Addresses, Port Numbers, and Data Munging
### Port Numbers
- Used by **TCP and UDP**.
- Help differentiate services (e.g., mail vs. web on the same machine).

### Byte Order Conversion Functions
- `htons()`, `htonl()` (Host to Network Byte Order).
- `ntohs()`, `ntohl()` (Network to Host Byte Order).

### Structs and Address Representation
#### `struct addrinfo`
```c
struct addrinfo {
    int ai_flags;         // AI_PASSIVE, AI_CANONNAME, etc.
    int ai_family;        // AF_INET, AF_INET6, AF_UNSPEC
    int ai_socktype;      // SOCK_STREAM, SOCK_DGRAM
    int ai_protocol;      // Use 0 for any
    size_t ai_addrlen;    // Size of ai_addr in bytes
    struct sockaddr *ai_addr;  // Destination address and port
    char *ai_canonname;   // Canonical hostname
    struct addrinfo *ai_next;  // Next node in the linked list
};
```
#### `struct sockaddr_in`
Used for **IPv4**.
```c
struct sockaddr_in {
    short int sin_family;    // Address family (AF_INET)
    unsigned short int sin_port;  // Port number (use htons())
    struct in_addr sin_addr; // IP address
    unsigned char sin_zero[8]; // Padding (set to 0 with memset())
};
```

### Manipulating IP Addresses
- `inet_pton(AF_INET, "1.1.1.1", &(struct))` → Converts IP to struct.
- `inet_ntop(AF_INET, &(struct), buffer, sizeOfIp)` → Converts struct to IP.

## System Calls
### `getaddrinfo()` - Prepare to Launch
```c
#include <sys/types.h>
#include <sys/socket.h>
#include <netdb.h>
int getaddrinfo(const char *node, const char *service,
                const struct addrinfo *hints, struct addrinfo **res);
```
### `socket()` - Get the File Descriptor
```c
#include <sys/types.h>
#include <sys/socket.h>
int socket(int domain, int type, int protocol);
```
### `bind()` - Assign a Port
```c
#include <sys/types.h>
#include <sys/socket.h>
int bind(int sockfd, struct sockaddr *my_addr, int addrlen);
```
### `connect()` - Connect to a Remote Host
```c
int connect(int sockfd, struct sockaddr *serve_addr, int addrlen);
```
### `listen()` - Wait for Connections
```c
int listen(int sockfd, int backlog);
```
### `accept()` - Accept Incoming Connections
```c
int accept(int sockfd, struct sockaddr *addr, socklen_t *addrlen);
```
### `send()` & `recv()` - Send and Receive Data
```c
int send(int sockfd, const void *msg, int len, int flags);
int recv(int sockfd, void *buf, int len, int flags);
```
### `close()` - Close the Socket
```c
close(sockfd);
```
### `getpeername()` - Get Connection Info
```c
int getpeername(int sockfd, struct sockaddr *addr, int *addrlen);
```

## Client-Server Model
1. The server waits for a connection.
2. Accepts the connection.
3. Forks a child process to handle the request.

## Advanced Techniques
### Non-Blocking Sockets
- By default, sockets are **blocking**.
- Use `fcntl()` with `O_NONBLOCK` to make them **non-blocking**.

### `poll()` - Monitor Multiple Sockets
```c
#include <poll.h>
int poll(struct pollfd fds[], nfds_t nfds, int timeout);
```
- **Efficient** for handling multiple connections.
- Each `pollfd` struct contains:
  ```c
  struct pollfd {
      int fd;          // Socket descriptor
      short events;    // Events to monitor (POLLIN, POLLOUT, POLLHUP)
      short revents;   // Events that occurred
  };
  ```

### `select()` - Old School Multiplexing
```c
int select(int numfds, fd_set *readfds, fd_set *writefds,
           fd_set *exceptfds, struct timeval *timeout);
```
- **Monitors multiple file descriptors at once.**
- Uses `fd_set` macros:
  - `FD_SET(fd, &set)` → Add socket.
  - `FD_CLR(fd, &set)` → Remove socket.
  - `FD_ISSET(fd, &set)` → Check if ready.
  - `FD_ZERO(&set)` → Clear set.

### Handling Partial `send()` Calls
- **`send()` may not send all bytes at once.**
- Use a loop to ensure the entire message is sent.

### Serialization - Packing Data
- **Encapsulation** ensures data is correctly interpreted.
- Example: Sending structured data in a binary format.

---
### References
- [Beej's Guide to Network Programming](https://beej.us/guide/bgnet/)
- Linux man pages (`man socket`, `man poll`, `man select`)

---
**Author:**
ABDELLAH BOUNAB
ANAS MEJDOUB
NOURDINE BENYAHYA

> _A detailed guide to understanding and working with sockets in networking._
