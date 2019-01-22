#Features
<ul>
<li>Add Category List Any Where</li>
<li>Automatic Pick Default Store Category as Parent</li>
<li>Category Image into List</li>
<li>Can Manage Image Size</li>
<li>Can Assign Custom Parent Category</li>
</ul>

After that, need to install this module as follows:
<pre>
  php composer require magento/magento-composer-installer
  php composer require benlau/categorywidget
</pre>


<h3> Enable Benlau/CategoryWidget Module</h3>
to Enable this module you need to follow these steps:

<ul>
<li>
<strong>Enable the Module</strong>
<pre>php bin/magento module:enable Benlau_CategoryWidget</pre></li>
<li>
<strong>Run Upgrade Setup</strong>
<pre>php bin/magento setup:upgrade</pre></li>
<li>
<strong>Re-Compile (in-case you have compilation enabled)</strong>
	<pre>php bin/magento setup:di:compile</pre>
</li>
</ul>

