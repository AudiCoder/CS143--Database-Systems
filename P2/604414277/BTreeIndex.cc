/*
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */
 
#include "BTreeIndex.h"
#include "BTreeNode.h"
#include <cmath>

using namespace std;

static const int nonLeafNodeKeys1 = floor((PageFile::PAGE_SIZE - sizeof(int) - sizeof(PageId))/(sizeof(int) + sizeof(PageId))) - 1;
static const int leafNodeKeys1 = floor((PageFile::PAGE_SIZE - sizeof(int) - sizeof(PageId))/(sizeof(int) + sizeof(RecordId))) - 1;
/*
 * BTreeIndex constructor
 */
BTreeIndex::BTreeIndex()
{
    rPid = -1;
}

/*
 * Open the index file in read or write mode.
 * Under 'w' mode, the index file should be created if it does not exist.
 * @param indexname[IN] the name of the index file
 * @param mode[IN] 'r' for read, 'w' for write
 * @return error code. 0 if no error
 */
RC BTreeIndex::open(const string& indexname, char mode)
{
    RC   rc;
    PageId epid;
    // OPENING fILE
    if ((rc = pf.open(indexname, mode)) < 0) return rc;
    epid = pf.endPid();
     if (epid == 0 && mode == 'r') 
        return RC_NO_SUCH_RECORD;
    else if (epid == 0 && mode == 'w') {
        rPid = 1;
        tHeight = 1;
        buf_read.buffer_int[0] = rPid;
        buf_read.buffer_int[1] = tHeight;
        pf.write(0, buf_read.buffer_char);
        BTLeafNode leafNode;
        leafNode.write(rPid, pf);
    }
	// height is stored in 1
	// root pid is stored in 0
	else {
        pf.read(0, buf_read.buffer_char);
        rPid = buf_read.buffer_int[0];
        tHeight = buf_read.buffer_int[1];
    }
    return 0;
}

/*
 * Close the index file.
 * @return error code. 0 if no error
 */
RC BTreeIndex::close()
{
    buf_read.buffer_int[1] = tHeight;
    pf.write(0, buf_read.buffer_char);
    pf.close();
    return 0;
}

/*
 * Insert (key, RecordId) pair to the index.
 * @param key[IN] the key for the value inserted into the index
 * @param rid[IN] the RecordId for the record being inserted into the index
 * @return error code. 0 if no error
 */
RC BTreeIndex::insert(int key, const RecordId& rid)
{
    BTLeafNode leafNode;
    BTNonLeafNode nonLeafNode;
    PageId page_id[tHeight];
    int h = 1;
    page_id[h-1] = rPid;
    
    while (h<tHeight) 
	{
        nonLeafNode.read(page_id[h-1], pf);
        nonLeafNode.locateChildPtr(key, page_id[h]);
        ++h;
    }
    
    leafNode.read(page_id[h-1], pf);

    if (leafNodeKeys1 != leafNode.getKeyCount()) 
	{
        leafNode.insert(key, rid);
        leafNode.write(page_id[h-1], pf);
    }
	else
	{
        BTLeafNode sibling;
        int siblingKey;
        leafNode.insertAndSplit(key, rid, sibling, siblingKey);
        PageId siblingPid;
        siblingPid = pf.endPid();
        sibling.write(siblingPid, pf);
        leafNode.setNextNodePtr(siblingPid);
        leafNode.write(page_id[h-1], pf);
        
        int midkey;
        int checker = h-2;
        if (checker >= 0) 
		{
            nonLeafNode.read(page_id[checker], pf);
            
            while (nonLeafNode.getKeyCount() == nonLeafNodeKeys1) 
			{
                BTNonLeafNode siblingNonLeaf;
                nonLeafNode.insertAndSplit(siblingKey, siblingPid, siblingNonLeaf, midkey);
                nonLeafNode.write(page_id[checker], pf);
                siblingPid = pf.endPid();
                siblingNonLeaf.write(siblingPid, pf);
                --checker;
                if (checker < 0) 
				{
                    siblingKey = midkey;
                    break;
                } 
				else 
				{
                    nonLeafNode.read(page_id[checker], pf);
                    siblingKey = midkey;
                }
            }
        }
        if (checker >= 0) 
		{
            nonLeafNode.insert(siblingKey, siblingPid);
            nonLeafNode.write(page_id[checker], pf);
        } 
		else
		{
            BTNonLeafNode root;
            root.initializeRoot(rPid, siblingKey, siblingPid);
            rPid = pf.endPid();
            root.write(rPid, pf);
            buf_read.buffer_int[0] = rPid;
            ++tHeight;
            buf_read.buffer_int[1] = tHeight;
        }
    } 

    return 0;
}

/*
 * Find the leaf-node index entry whose key value is larger than or 
 * equal to searchKey, and output the location of the entry in IndexCursor.
 * IndexCursor is a "pointer" to a B+tree leaf-node entry consisting of
 * the PageId of the node and the SlotID of the index entry.
 * Note that, for range queries, we need to scan the B+tree leaf nodes.
 * For example, if the query is "key > 1000", we should scan the leaf
 * nodes starting with the key value 1000. For this reason,
 * it is better to return the location of the leaf node entry 
 * for a given searchKey, instead of returning the RecordId
 * associated with the searchKey directly.
 * Once the location of the index entry is identified and returned 
 * from this function, you should call readForward() to retrieve the
 * actual (key, rid) pair from the index.
 * @param key[IN] the key to find.
 * @param cursor[OUT] the cursor pointing to the first index entry
 *                    with the key value.
 * @return error code. 0 if no error.
 */
RC BTreeIndex::locate(int searchKey, IndexCursor& cursor)
{
    BTNonLeafNode nonLNode;
    PageId pid = rPid;
    RC rc;
    int i = 1;
    while (i<tHeight) {
        nonLNode.read(pid, pf);
        nonLNode.locateChildPtr(searchKey, pid);
        i++;
    }
    
    BTLeafNode lNode;
    int eid;
    lNode.read(pid, pf);
    rc = lNode.locate(searchKey, eid);
	if (rc == RC_NO_SUCH_RECORD) 
        return rc;
    
    cursor.pid = pid;
    cursor.eid = eid;
    
    return 0;
}

/*
 * Read the (key, rid) pair at the location specified by the index cursor,
 * and move foward the cursor to the next entry.
 * @param cursor[IN/OUT] the cursor pointing to an leaf-node index entry in the b+tree
 * @param key[OUT] the key stored at the index cursor location.
 * @param rid[OUT] the RecordId stored at the index cursor location.
 * @return error code. 0 if no error
 */
RC BTreeIndex::readForward(IndexCursor& cursor, int& key, RecordId& rid)
{
    BTLeafNode lNode;
	// read the page
    lNode.read(cursor.pid, pf);
    // REDING THE eID  and fetching key and rid
	lNode.readEntry(cursor.eid, key, rid);
    
    if (cursor.eid == lNode.getKeyCount())
	{
        PageId nextpid;
        nextpid = lNode.getNextNodePtr();
        if (nextpid == -1) 
            return RC_END_OF_TREE;
        cursor.pid = nextpid;
        cursor.eid = 1;
    }
	else 
        cursor.eid++;
		
    return 0;
}
