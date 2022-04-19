# Karaka Setup

A good way to setup a demo application with mostly randomly generated user input data is this setup script.

The following command will create a demo application:

```sh
php demoSetup/setup.php
```

In some cases code changes may require changes to the demo setup script (e.g. changes in the api, new modules). Since the demo setup script tries to simulate user generated data it takes some time to run. You may speed up the runtime by parallelizing the execution. However, this may use up 100% of your CPU and storage performance.

```sh
php demoSetup/setup.php -a 0
```
