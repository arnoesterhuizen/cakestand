Cakestand
=========

Cakestand for CakePHP: Experiments with the CakePHP Rapid Development Framework

This project is built on the CakePHP framework, and intends to bridge the space between the framework and your application. It is intended to be a smarter scaffolding, one that could possibly be pointed to your CakePHP-normalised database and taken live.

Basic features like data validation is included.

I'll also be experimenting with various enterprise design patterns like broadcasters/observers, and adding small touches like API throttling and using bitmasks. I'd like to include workflows, too, possibly using petrinets.

But just as important is the interface. This includes beautiful HTML front-ends with small accessibility features like accesskeys for form fields, as well as usability features like intelligent data filtering mechanisms.

AJAX, XML and JSON interfaces will be included as well.

FEATURES
Patterns
* Observer

Models
* Sortable if they have an "order" column

HTML
* List
  * Paginator
  * Breadcrumb bar that allows model ancestry (Cities are children of Countries, etc)
  * Grouping in tables
  * Filter
  * Order
* Add/Edit/Search/All Forms
  * Accesskeys

API
* Keyring with ACL permissions in Session
* Methods
  * list
  * edit
  * add
  * view
  * delete
  * relationships?
  * summary
  * toggle using bitmasks
  * recycle bin
  * undelete
* Interface
* Flood control and throttling

Data Packet
* entity
  * singularVar
  * pluralVar
  * singularHumanName
  * pluralHumanName
* data
* paging
	* first
	* prev
	* next
	* last

http://mikemayo.org/2012/how-i-learned-to-stop-worrying-and-love-rest
http://pastie.org/3326375

I18N
* domains
	* header
	* helptext

Bitmask values configured in a central registry
$bitmasks[modelname][fieldname] = array()

notes: To read header link values: $this->request->header('Link');
