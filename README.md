# TCVR is a simple database.
# Author Paul Alagna PJAlagna@Gmail.com

Tuple Centered Variable Reterival 
also the acronym of the elements
T is the main collector (roughly the "table")
C is the elemental collection (roughly the "Column")
V is the value
R is the second collector (roughly the "key")



there are rules on the tuple
	-a TCR will yield (return) one and only one V
		IE given values for T,C,and R will return only 1 value for V
	-b R is unique throughout the system.
	-c T is unique throughout the system.
	
query rules
query(T,C,V,R) => y/n eg existence IF T,C,V,R are not blank.

the following is true for all non blank values of the parameters mentioned:

query(t,,,) => [c] eg the set of c's collected under t
(t,c,,) => [v] eg the domain of c
(t,,,r) => [c] the set of c collected under that sub-collection
----  (t,,,0) => the structure of t. ** this is maintained by convention (ie a product of the API)
(,c,,) => [t] everywhere this element is used. useful for data aging analysis
(,,,r) [t] everywhere this subcollector is used (eg all subtables) (useful for data abstraction)
(t,c,v,) => [r] all records where c=v. eg all records where "firstName" has a value of "Paul"
(,,,,) => [t] ie the names of all collections ("tables") in the system.
(t,c,,r) => v eg value of c in collection t under subcollector r. 
