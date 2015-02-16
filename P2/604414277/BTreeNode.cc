#include "BTreeNode.h"
#include <cmath>

using namespace std;

static const int Size_Buf_int = PageFile::PAGE_SIZE/sizeof(int);

static const int Key_leaf_Nde = floor((PageFile::PAGE_SIZE - sizeof(int) - sizeof(PageId))/(sizeof(int) + sizeof(RecordId))) - 1;
static const int Keys_minimum_Leaf_Node = ceil(Key_leaf_Nde / 2);

static const int Keys_nonLeaf = floor((PageFile::PAGE_SIZE - sizeof(int) - sizeof(PageId))/(sizeof(int) + sizeof(PageId))) - 1;
static const int Key_minimum_NonLeaf = ceil((Keys_nonLeaf + 1) / 2) - 1;

/*
 *Constructor to initialize the Leaf Node. It will assign -1 to all elements in the buf_read.
*/
BTLeafNode::BTLeafNode()
{
	int x=0;
	while( x<Size_Buf_int)
	{
		buf_read.buffer_int[x] = -1;
		x++;
	}
}

/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::read(PageId pid, const PageFile& pf)
{
	int rc1=pf.read(pid, buf_read.buffer_char);
	return rc1;
}
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::write(PageId pid, PageFile& pf)
{
	int rc1=pf.write(pid, buf_read.buffer_char);
	return rc1; 
}
/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 * Count is stored in the first element of the integer buf_read.
 */
int BTLeafNode::getKeyCount()
{
	if(buf_read.buffer_int[0] == -1)
		return 0;
	else
		return buf_read.buffer_int[0];
}

/*
 * Insert a (key, rid) pair to the node.
 * @param key[IN] the key to insert
 * @param rid[IN] the RecordId to insert
 * @return 0 if successful. Return an error code if the node is full.
 * Leaf-Node:
 * 1st Element contains number of keys.
 * then pair of (rid,key) is stored.
 * Last element points to th next leaf node.
 * There is an extra space to take care of split case (overflow case).
 */
RC BTLeafNode::insert(int key, const RecordId& rid)
{
	int k_count, x=3;
	k_count = getKeyCount();

	if(k_count == Key_leaf_Nde) {
		return RC_NODE_FULL;
	}
	while( x<=k_count*3)
	{
		if(buf_read.buffer_int[x] > key)
			break;
			x=x+3;
	}

	for (int j=k_count*3;j>=x-2; j--)
		buf_read.buffer_int[j+3] = buf_read.buffer_int[j];

	buf_read.buffer_int[x-2] = (int) rid.pid;
	buf_read.buffer_int[x-1] = (int) rid.sid;
	buf_read.buffer_int[x] = key;


	buf_read.buffer_int[0] = k_count+1;
	return 0;
}

/*
 * Insert the (key, rid) pair to the node
 * and split the node half and half with sibling.
 * The first key of the sibling node is returned in siblingKey.
 * @param key[IN] the key to insert.
 * @param rid[IN] the RecordId to insert.
 * @param sibling[IN] the sibling node to split with. This node MUST be EMPTY when this function is called.
 * @param siblingKey[OUT] the first key in the sibling node after split.
 * @return 0 if successful. Return an error code if there is an error.
 * Leaf-Node:
 * 1st Element contains number of keys.
 * then pair of (rid,key) is stored.
 * Last element points to th next leaf node.
 * There is an extra space to take care of split case (overflow case).
 */
RC BTLeafNode::insertAndSplit(int key, const RecordId& rid, 
                              BTLeafNode& sibling, int& siblingKey)
{
	int k_count, x=3;
	k_count = getKeyCount();

	//Check for first largest key.
	while( x<=k_count*3)
	{
		if(buf_read.buffer_int[x] > key)
			break;
			x=x+3;
	}

	
	for (int j=k_count*3;j>=x-2; j--)
		buf_read.buffer_int[j+3] = buf_read.buffer_int[j];

	buf_read.buffer_int[x-2] = (int) rid.pid;
	buf_read.buffer_int[x-1] = (int) rid.sid;
	buf_read.buffer_int[x] = key;
	
	buf_read.buffer_int[0] = k_count+1;
	int c=Keys_minimum_Leaf_Node*3+6;
	//Moving the right half of the node to the sibling node.
	for (x=c; x<Size_Buf_int-1; x = x+3) {
		RecordId t_rid;
		t_rid.pid = buf_read.buffer_int[x-2];
		t_rid.sid = buf_read.buffer_int[x-1];
		sibling.insert(buf_read.buffer_int[x], t_rid);
	}
	int y=Size_Buf_int-1;
	//Setting the next pointer of the sibling node.
	PageId next1 = (PageId) buf_read.buffer_int[y];
	sibling.setNextNodePtr(next1);

	//Getting the first key of the sibling node.
	siblingKey = buf_read.buffer_int[c];

	//Removing the moved values from the current node by seeting the array elements to -1
	for (x=c-2; x<Size_Buf_int-1; x++)
		buf_read.buffer_int[x] = -1;
	buf_read.buffer_int[0] = Keys_minimum_Leaf_Node+1;
	//buf_read.buffer_int[Size_Buf_int-1] = 
	return 0;
}

/*
 * Find the entry whose key value is larger than or equal to searchKey
 * and output the eid (entry number) whose key value >= searchKey.
 * Remeber that all keys inside a B+tree node should be kept sorted.
 * @param searchKey[IN] the key to search for
 * @param eid[OUT] the entry number that contains a key larger than or equalty to searchKey
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::locate(int searchKey, int& eid)
{
	int k_count;
	k_count = getKeyCount();
	int set = 0, x=3;

	//Check for first equal or largest key.
	while(x<=k_count*3)
	{
		if(buf_read.buffer_int[x] >= searchKey)
		{
			set = 1;
			break;
		}
		x=x+3;
	}

	if(set != 0) 
		eid = x/3;
	else 
		return RC_NO_SUCH_RECORD;
		
	return 0;
}

/*
 * Read the (key, rid) pair from the eid entry.
 * @param eid[IN] the entry number to read the (key, rid) pair from
 * @param key[OUT] the key from the entry
 * @param rid[OUT] the RecordId from the entry
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::readEntry(int eid, int& key, RecordId& rid)
{
	if (eid < 0 || eid > getKeyCount())
		return RC_INVALID_CURSOR;

	//eid is multiplied by 3 to get the array index.
	key = buf_read.buffer_int[eid*3];

	rid.pid = buf_read.buffer_int[eid*3-2];
	rid.sid = buf_read.buffer_int[eid*3-1];
	
	return 0;
}

/*
 * Return the pid of the next slibling node.
 * @return the PageId of the next sibling node 
 */
PageId BTLeafNode::getNextNodePtr()
{
	int f=Size_Buf_int-1;
	PageId p = (PageId) buf_read.buffer_int[f];
	return p;
}

/*
 * Set the pid of the next slibling node.
 * @param pid[IN] the PageId of the next sibling node 
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::setNextNodePtr(PageId pid)
{
int f=Size_Buf_int-1;
	if((int) pid < -1) 
		return RC_INVALID_PID;
	buf_read.buffer_int[f] = (int) pid;
	return 0;
}

/*
 *Constructor to initialize the Non-Leaf Node. It will assign -1 to all elements in the buf_read.
*/
BTNonLeafNode::BTNonLeafNode()
{
	for(int i=0; i<Size_Buf_int; i++)
		buf_read.buffer_int[i] = -1;
}

/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::read(PageId pid, const PageFile& pf)
{
	return pf.read(pid, buf_read.buffer_char);
}
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::write(PageId pid, PageFile& pf)
{
	return pf.write(pid, buf_read.buffer_char);
}

/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 * Count is stored in the first element of the integer buf_read.
 */
int BTNonLeafNode::getKeyCount()
{
	if(buf_read.buffer_int[0] == -1)
		return 0;
	else
		return buf_read.buffer_int[0];
}

/*
 * Insert a (key, pid) pair to the node.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @return 0 if successful. Return an error code if the node is full.
 * Non-Leaf-Node:
 * 1st Element contains number of keys.
 * then a page id to left most sub node.
 * then pairs of (key,page id).
 * There is an extra space to take care of split case (overflow case).
 */
RC BTNonLeafNode::insert(int key, PageId pid)
{
	int k_count, x;
	k_count = getKeyCount();

	//If node is full return error.
	if(k_count == Keys_nonLeaf) {
		return RC_NODE_FULL;
	}
	x=2;
	//Check for first largest key.
	while(x<=k_count*2)
	{
		if(buf_read.buffer_int[x] > key)
			break;
			x=x+2;
	}

	//Shift all keys greater than new key.
	for (int j=k_count*2+1;j>=x; j--)
		buf_read.buffer_int[j+2] = buf_read.buffer_int[j];
	buf_read.buffer_int[x] = key;
	buf_read.buffer_int[x+1] = (int) pid;
	buf_read.buffer_int[0] = k_count+1;
	return 0;
}

/*
 * Insert the (key, pid) pair to the node
 * and split the node half and half with sibling.
 * The middle key after the split is returned in midKey.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @param sibling[IN] the sibling node to split with. This node MUST be empty when this function is called.
 * @param midKey[OUT] the key in the middle after the split. This key should be inserted to the parent node.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::insertAndSplit(int key, PageId pid, BTNonLeafNode& sibling, int& midKey)
{
	int k_count, x;
	k_count = getKeyCount();

	x=2;
	while(x<=k_count*2)
	{
		if(buf_read.buffer_int[x] > key)
			break;
			x=x+2;
	}
int j=k_count*2+1;
	//Shift all keys greater than new key.
	while(j>=x)
	{
		buf_read.buffer_int[j+2] = buf_read.buffer_int[j];
		j--;
	}
	
	buf_read.buffer_int[x] = key;
	buf_read.buffer_int[x+1] = (int) pid;
	buf_read.buffer_int[0] = k_count+1;

	//Initializing the new sibling non-leaf node.
	int c=Key_minimum_NonLeaf*2+3;
	int d=Key_minimum_NonLeaf*2+5;
	PageId p1 = (PageId) buf_read.buffer_int[c];
	PageId p2 = (PageId) buf_read.buffer_int[d];

	sibling.initializeRoot(p1, buf_read.buffer_int[c+1], p2);
	x=d+1;
	//Copying the right half to the sibling node.
	while( x<Size_Buf_int) {
		PageId p = (PageId) buf_read.buffer_int[x+1];
		sibling.insert(buf_read.buffer_int[x], p);
		x=x+2;
	}

	//Setting the mid key variable.
	midKey = buf_read.buffer_int[c-1];

	//Setting the right half of the current buffer to -1
	for (x=c-1; x<Size_Buf_int; x++) 
		buf_read.buffer_int[x] = -1;

	buf_read.buffer_int[0] = Key_minimum_NonLeaf;
	return 0;
}

/*
 * Given the searchKey, find the child-node pointer to follow and
 * output it in pid.
 * @param searchKey[IN] the searchKey that is being looked up.
 * @param pid[OUT] the pointer to the child node to follow.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::locateChildPtr(int searchKey, PageId& pid)
{
	int k_count= getKeyCount();
	if(k_count == 0) {
		return RC_NO_SUCH_RECORD;
	}
	int set = 0, x;
	for (x=2; x<=k_count*2; x=x+2)
	{
		if(buf_read.buffer_int[x] > searchKey){
			set = 1;
			break;
		}		
	}
	PageId p1;
	if(set != 0) 
		p1 = (PageId) buf_read.buffer_int[x-1];
		else 
		p1 = (PageId) buf_read.buffer_int[k_count*2+1];
	
	pid = p1;
	return 0;
}

/*
 * Initialize the root node with (pid1, key, pid2).
 * @param pid1[IN] the first PageId to insert
 * @param key[IN] the key that should be inserted between the two PageIds
 * @param pid2[IN] the PageId to insert behind the key
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::initializeRoot(PageId pid1, int key, PageId pid2)
{
	for(int i=0; i<256; i++)
		buf_read.buffer_int[i] = -1;

	buf_read.buffer_int[0] = 1;
	buf_read.buffer_int[1] = (int) pid1;
	buf_read.buffer_int[2] = key;
	buf_read.buffer_int[3] = (int) pid2;
	return 0;
}
