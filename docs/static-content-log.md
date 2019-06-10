# Static content log

This log will be used to generate migrations for static content. Any static blocks, pages, or custom BlueFoot blocks & attributes that are created or updated should be included here.

For upgrade projects, any pre-existing entities that are updated during
the project must also be included. E.g., if the Magento 1 site had a static
block called footer_links and you update it during the Magento 2 build, it must
be included in this log and marked as an update.

If you updated an existing BlueFoot block in the admin (e.g., by adding attributes), it must also be included as an update.


## Static blocks

Identifier | Create | Update
---------- | ------ | ------
`just_an_example` | | x

## Pages

Identifier         | Create | Update
------------------ | ------ | ------
`just_an_example` | x | 

## BlueFoot blocks

Identifier         | Create | Update
------------------ | ------ | ------
`just_an_example` | x | 

## BlueFoot attributes

Identifier         | Create | Update
------------------ | ------ | ------
`just_an_example` | x | 