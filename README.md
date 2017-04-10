# TCVR is a simple database.
# Author Paul Alagna PJAlagna@Gmail.com
# PJA 04-13-2000 original

<br/> Tuple Centered Variable Reterival 
<br/> also the acronym of the elements
<br/> T is the main collector (roughly the "table")
<br/> C is the elemental collection (roughly the "Column")
<br/> V is the value
<br/> R is the second collector (roughly the "key")
<br/> 
<br/> 
<br/> 
<br/> there are rules on the tuple
	<br/> -a TCR will yield (return) one and only one V
		<br/> IE given values for T,C,and R will return only 1 value for V
	<br/> -b R is unique throughout the system.
	<br/> -c T is unique throughout the system.
	
<br/> query rules
<br/> query(T,C,V,R) => y/n eg existence IF T,C,V,R are not blank.
<br/> if all are blank IE query (,,,,) => [t] ie the names of all collections ("tables") in the system.

<br/> the following is true for all non blank values of the parameters mentioned:

<br/> query(t,,,) => [c] eg the set of c's collected under t
<br/> (t,c,,) => [v] eg the domain of c
<br/> (t,,,r) => [c] the set of c collected under that sub-collection
<br/> ----  (t,,,0) => the structure of t. ** this is maintained by convention (ie a product of the API)
<br/> (,c,,) => [t] everywhere this element is used. useful for data aging analysis
<br/> (,,,r) [t] everywhere this subcollector is used (eg all subtables) (useful for data abstraction)
<br/> (t,c,v,) => [r] all records where c=v. eg all records where "firstName" has a value of "Paul"
<br/>(t,c,,r) => v eg value of c in collection t under subcollector r. 
