# SupervisorBundle [![Build Status](https://secure.travis-ci.org/yzalis/SupervisorBundle.png)](http://travis-ci.org/yzalis/SupervisorBundle)

```
yz_supervisor:
    default_environment: dev
    servers:
        prod:
            SUPERVISOR_01:
                host: 192.168.0.1
                username: guest
                password: password
                port: 9001
            SUPERVISOR_02:
                host: 192.168.0.2
                username: guest
                password: password
                port: 9001
        dev:
            locahost:
                host: 127.0.0.1
                username: guest
                password: password
                port: 9001
```


## Credits

* Benjamin Laugueux <benjamin@yzalis.com>
* [All contributors](https://github.com/yzalis/SupervisorBundle/contributors)

## License

Supervisor is released under the MIT License. See the bundled LICENSE file for details.
