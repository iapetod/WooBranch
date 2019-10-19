# WooBranch

Plugin for the integration of store branches

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

It is necessary to have Wordpress installed

```
$ wp core install --url=example.com --title=Example --admin_user=supervisor --admin_password=strongpassword --admin_email=info@example.com
```

or

In addition to wordpress, it is necessary to have woocommerce installed for full integration


```
wp plugin install woocommerce --activate
```

or download and install zip in https://woocommerce.com/


### Installing


```
cd path_wordpress/wp-content/plugins
mkdir branch
cd branch
git clone https://github.com/iapetod/WooBranch.git
```

In Wordpress Path

```
wp plugin activate branch
```

or zip the bsale folder and install the plugin from the wordpress dashboard

## Features

* Creation of store branches
* Integration in the shipping calculator
* Select shipping branch


## Authors

* **Jesus Marcano** - *Initial work* - [Iapetod](https://github.com/iapetod)

See also the list of [contributors](https://github.com/iapetod/WooBranch/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

