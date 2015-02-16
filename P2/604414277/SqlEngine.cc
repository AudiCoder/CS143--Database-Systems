/**
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */

#include <cstdio>
#include <iostream>
#include <fstream>
#include "Bruinbase.h"
#include "SqlEngine.h"
#include "BTreeIndex.h"

using namespace std;

// external functions and variables for load file and sql command parsing
extern FILE* sqlin;
int sqlparse(void);


RC SqlEngine::run(FILE* commandline)
{
    fprintf(stdout, "Bruinbase> ");
    
    // set the command line input and start parsing user input
    sqlin = commandline;
    sqlparse();  // sqlparse() is defined in SqlParser.tab.c generated from
    // SqlParser.y by bison (bison is GNU equivalent of yacc)
    
    return 0;
}

RC SqlEngine::select(int attr, const string& table, const vector<SelCond>& cond)
{
    RecordFile rf;   // RecordFile containing the table
    RecordId   rid;  // record cursor for table scanning
    BTreeIndex treeIndex;
    
    RC     rc;
    int    key = 0;
    string value;
    int    count;
    int    diff;
    bool index = true;
    int keyCount = 0;
    IndexCursor cursor;
    int notEqualKeys[cond.size()];
    int equals = -1, var_minimum = 0, var_maximum = RC_END_OF_TREE;

    // open the table file
    if ((rc = rf.open(table + ".tbl", 'r')) < 0) {
        fprintf(stderr, "Error: table %s does not exist\n", table.c_str());
        return rc;
    }
    
    if (treeIndex.open(table + ".idx", 'r') < 0) 
        index = false;
    
    for (unsigned i = 0; i < cond.size(); i++)
	{
        if (cond[i].attr == 1) 
		{
            keyCount++;
            notEqualKeys[i] = -1;
            switch (cond[i].comp) 
			{
                case SelCond::LE:
                    if (atoi(cond[i].value) < var_maximum) 
                        var_maximum = atoi(cond[i].value);
                    else if (var_maximum == RC_END_OF_TREE) 
                        var_maximum = atoi(cond[i].value);
                    break;
				case SelCond::GE:
                    if (atoi(cond[i].value) > var_minimum) 
                        var_minimum = atoi(cond[i].value);
                    break;
				case SelCond::LT:
                    if (atoi(cond[i].value) < var_maximum) 
                        var_maximum = atoi(cond[i].value) - 1;
                    else if (var_maximum == RC_END_OF_TREE) 
                        var_maximum = atoi(cond[i].value) - 1;
                    break;
				case SelCond::GT:
                    if (atoi(cond[i].value) > var_minimum) 
                        var_minimum = atoi(cond[i].value) + 1;
                    break;
				case SelCond::EQ:
                    if (equals != -1 && equals != atoi(cond[i].value)) 
                        goto exit_select;
                    else 
                        equals = atoi(cond[i].value);
                    break;
                case SelCond::NE:
                    notEqualKeys[i] = atoi(cond[i].value);
                    keyCount--;
                    break;
            }
        }
    }
    
    if (var_maximum != RC_END_OF_TREE && (var_maximum < var_minimum || (equals != -1 && (equals < var_minimum || equals > var_maximum))))
        goto exit_select;
    
    
    if (keyCount == 0) 
        index = false;
    
    count = 0;
    
    if (index) 
	{
        if (equals != -1) 
		{
            rc = treeIndex.locate(equals, cursor);
            if (rc == RC_NO_SUCH_RECORD) 
                goto exit_select;
            
            rc = treeIndex.readForward(cursor, key, rid);
            diff = key - equals;
            if (diff == 0) 
			{
                for (unsigned i = 0; i < cond.size(); i++) 
				{
                    if (cond[i].attr == 1 && cond[i].comp == SelCond::NE) 
					{
                        if (equals == atoi(cond[i].value)) 
                            goto exit_select;
                    } 
					else if (cond[i].attr == 2)
					{
                        rf.read(rid, key, value);
                        diff = strcmp(value.c_str(), cond[i].value);
                        switch (cond[i].comp) 
						{
                            case SelCond::LE:
                                if (diff > 0) 
									goto exit_select;
                                break;
							case SelCond::GE:
                                if (diff < 0) 
									goto exit_select;
                                break;
							case SelCond::LT:
                                if (diff >= 0) 
									goto exit_select;
                                break;
							case SelCond::GT:
                                if (diff <= 0) 
									goto exit_select;
                                break;
							case SelCond::EQ:
                                if (diff != 0) 
									goto exit_select;
                                break;
                            case SelCond::NE:
                                if (diff == 0) 
									goto exit_select;
                                break;
                        }
                    }
                }
                count++;
                switch (attr)
				{
                    case 3:  
                        rf.read(rid, key, value);
                        fprintf(stdout, "%d '%s'\n", key, value.c_str());
                        break;
					case 2:  
                        rf.read(rid, key, value);
                        fprintf(stdout, "%s\n", value.c_str());
                        break;
					case 1:  
                        fprintf(stdout, "%d\n", key);
                        break;
                }
                goto exit_select;
            } 
			else 
			    goto exit_select;
        }
        
        int p = var_minimum;
        
        rc = treeIndex.locate(p, cursor);
        
		if (rc == RC_NO_SUCH_RECORD) 
            goto exit_select;
        
        
		rc = treeIndex.readForward(cursor, key, rid);
		
	   bool var_temporary = false;
        
        if (rc == RC_END_OF_TREE && var_temporary == false) 
            var_temporary = true;
        
        p = key;
        bool var_verify = true;
        if (var_maximum != RC_END_OF_TREE) 
		{
            if (p > var_maximum) 
                var_verify = false;
        }
		
        while (var_verify) 
		{
            
            for (unsigned i = 0; i < cond.size(); i++) 
			{
                
                if (cond[i].attr == 1 && cond[i].comp == SelCond::NE) 
				{
                    if (p == atoi(cond[i].value)) 
                        goto end_point2;
                } 
				else if (cond[i].attr == 2) 
				{
                    rf.read(rid, key, value);
                    diff = strcmp(value.c_str(), cond[i].value);
                    switch (cond[i].comp)
					{
                        case SelCond::LE:
                            if (diff > 0) goto end_point2;
                            break;
						case SelCond::GE:
                            if (diff < 0) goto end_point2;
                            break;
						case SelCond::LT:
                            if (diff >= 0) goto end_point2;
                            break;
						case SelCond::GT:
                            if (diff <= 0) goto end_point2;
                            break;
						case SelCond::EQ:
                            if (diff != 0) goto end_point2;
                            break;
                        case SelCond::NE:
                            if (diff == 0) goto end_point2;
                            break;
                    }
                }
            }
            count++;
            switch (attr) 
			{
                case 1:  
                    fprintf(stdout, "%d\n", key);
                    break;
                case 2:  
                    rf.read(rid, key, value);
                    fprintf(stdout, "%s\n", value.c_str());
                    break;
                case 3:  
                    rf.read(rid, key, value);
                    fprintf(stdout, "%d '%s'\n", key, value.c_str());
                    break;
            }

        end_point2:
            rc = treeIndex.readForward(cursor, key, rid);
            p = key;
            if (rc == RC_END_OF_TREE && var_temporary == false)
			{
                var_temporary = true;
            } 
			else if (rc == RC_END_OF_TREE) 
			{
                goto exit_select;
            }
            if (var_maximum != RC_END_OF_TREE && p > var_maximum)
			{
                var_verify = false;
            }
        }
        goto exit_select;
    }
    
    
    // scan the table file from the beginning
    rid.pid = rid.sid = 0;
    count = 0;
    while (rid < rf.endRid()) {
        // read the tuple
        if ((rc = rf.read(rid, key, value)) < 0) {
            fprintf(stderr, "Error: while reading a tuple from table %s\n", table.c_str());
            goto exit_select;
        }
        
        // check the conditions on the tuple
        for (unsigned i = 0; i < cond.size(); i++) {
            // compute the difference between the tuple value and the condition value
            switch (cond[i].attr) {
                case 1:
                    diff = key - atoi(cond[i].value);
                    break;
                case 2:
                    diff = strcmp(value.c_str(), cond[i].value);
                    break;
            }
            
            // skip the tuple if any condition is not met
            switch (cond[i].comp) {
                case SelCond::EQ:
                    if (diff != 0) goto next_tuple;
                    break;
                case SelCond::NE:
                    if (diff == 0) goto next_tuple;
                    break;
                case SelCond::GT:
                    if (diff <= 0) goto next_tuple;
                    break;
                case SelCond::LT:
                    if (diff >= 0) goto next_tuple;
                    break;
                case SelCond::GE:
                    if (diff < 0) goto next_tuple;
                    break;
                case SelCond::LE:
                    if (diff > 0) goto next_tuple;
                    break;
            }
        }
        
        // the condition is met for the tuple.
        // increase matching tuple counter
        count++;
        
        // print the tuple
        switch (attr) {
            case 1:  // SELECT key
                fprintf(stdout, "%d\n", key);
                break;
            case 2:  // SELECT value
                fprintf(stdout, "%s\n", value.c_str());
                break;
            case 3:  // SELECT *
                fprintf(stdout, "%d '%s'\n", key, value.c_str());
                break;
        }
        
        // move to the next tuple
    next_tuple:
        ++rid;
    }
    
    // close the table file and return
exit_select:
    if (attr == 4) {
        fprintf(stdout, "%d\n", count);
    }
    rc = 0;

    rf.close();
    if (index) {
        treeIndex.close();
    }
    return rc;
}

RC SqlEngine::load(const string& table, const string& loadfile, bool index)
{
    
	string line;
	string value;
	int key;
	RC rc;
	BTreeIndex treeIndex;
	RecordId rid;
	RecordFile recfile;   
    ifstream file_object(loadfile.c_str());
    rc = recfile.open(table + ".tbl", 'w');
    if (rc < 0) 
	{
        fprintf(stderr, "Error: Not able to create table %s\n", table.c_str());
        return rc;
    }
    rid = recfile.endRid();
    
    if (index) 
	treeIndex.open(table + ".idx", 'w');
 
    while (getline(file_object, line))
	{
        parseLoadLine(line, key, value);
        recfile.append(key, value, rid);
        if (index)
		{
            treeIndex.insert(key, rid);
        }
        rid++;
    }
    
    recfile.close();
	
    if (index)
	treeIndex.close();
    
    file_object.close();
    
    return 0;
}

RC SqlEngine::parseLoadLine(const string& line, int& key, string& value)
{
    const char *s;
    char        c;
    string::size_type loc;
    
    // ignore beginning white spaces
    c = *(s = line.c_str());
    while (c == ' ' || c == '\t') { c = *++s; }
    
    // get the integer key value
    key = atoi(s);
    
    // look for comma
    s = strchr(s, ',');
    if (s == NULL) { return RC_INVALID_FILE_FORMAT; }
    
    // ignore white spaces
    do { c = *++s; } while (c == ' ' || c == '\t');
    
    // if there is nothing left, set the value to empty string
    if (c == 0) { 
        value.erase();
        return 0;
    }
    
    // is the value field delimited by ' or "?
    if (c == '\'' || c == '"') {
        s++;
    } else {
        c = '\n';
    }
    
    // get the value string
    value.assign(s);
    loc = value.find(c, 0);
    if (loc != string::npos) { value.erase(loc); }
    
    return 0;
}
