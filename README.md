#prerequisits
* php >=5.5.9
* symfony 3.1.x (see composer.json for details)
* linux based OS
* mysql >=5.5
* apache / nginx 

#installation
### composer:
```composer require mittax/wsse-bundle```

#usage
## Consume a WSSE secured api method.

After installation all defined firewallrules requests are secured with a X-WSSE Header. Generate a X-WSSE Header:

```php 
//your clientobject
$client = $this->container->get('mittax_wsse.client.service.http.request');

//API Url you want to call
$uri = 'http://<yourdomain>/wsse/username';

//the username who wants to access. (Must exists in your user database)
$username = 'yourusername';

//the wsse header to consume secured api
$headerOptions = $client->getWsseHeaderRequestOtionsByUsername($username);

//your serverresponse
$response = $client->request('GET', $uri, $headerOptions);
$response = (string)$response->getBody();

```

##generate a wsse header directly on a commandline
### open a terminal to your projectroot
```php bin/console mittax:wsse:generate-header```

This will output your headerstring like that.

```UsernameToken Username="mittax", PasswordDigest="ZWQ0MWRiMGFhODhlYTI0M2FlMGZiNDk4NzY5MWNjMmJhMDcyN2ZmZmQ4YTE1YTVhYTAxMTkzMjkxNTYxYWYwM2Y3YjMyZmVhYjJmMjBjNWM4ODFiYjliYzBiZDgxMjE0ZWUyYmUzYjFiODg5MmJmN2I2NTI2ZTk0NDZmNDM3ZDI=", Nonce="MTMyNTgzYTFjNDMxOWJlOA==", Created="2016-11-26T23:35:31+0000"```

Put that headerstring with a headervariable X-WSSE on your request (Postman for instance) 

###define firewall rules
app/config/comfig.yml

By default FOS_Userbundle is used with following configuration:
```yaml 
fos_user:
    db_driver: orm # other valid values are 'mongodb', 'couchdb' and 'propel'
    firewall_name: wsse_secured
    user_class: Mittax\WsseBundle\Entity\User #you can use the default fos_user implementation, the mittax entity or your own entityclass
```

You can define your routes in the securitysection

app/config/security.yml

```yaml
security:
      firewalls:
        wsse_secured:
          anonymous: false
          pattern: ^/wsse|your_path_here
          stateless: true
          wsse:      { lifetime: 60 }
```

## configure your security wsse layer. Try to move the vendor config to your config
src/vendor/mittax/wsse-bundle/Resources/config.yml

```yaml
#configure the wsse
mittax_wsse:
    #change that for your project
    salt: cf83e1357eefb8bdf1542850d66d8007d620e4050b5715dc83f4a921d36ce9ce47d0d13c5d85f2b0ff8318d2877eec2f63b931bd47417a81a538327af927da3e

    # the lifetime a userdigist is valid
    lifetime: 600

    #the class to encode the digest
    encoder: Mittax\WsseBundle\Security\Encoder\Sha512

    #if true the client nonce is valid for just one request to prevent relay attacks
    # for development you can disable this securityfeature
    # preventreplayattacks: false
    preventreplayattacks: true

    # by default we are depending on this usermanager. Feel free to use your own usermanager
    usermanager: fos_user.user_manager

    # tablename where the userpasswords are stored
    usertablename: fos_user

    # columnname where username is stored
    usernamecolumn: username

    # name of the column where the passwords are stored
    passwordcolumn: password

    #################################################################################################
    # CHANGE ME !!!!!
    # UNITTEST / Functional Test. This username is required to run unittests
    # Add you own user here
integrationtestsusername: mittax
```

 

   
