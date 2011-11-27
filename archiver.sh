# This script accepts a github repo url, clones it, and merges it into the archive project

# Require project repo url as 1st arg
if [ ! $1 ]; then
  echo 'project url required, e.g., $ bash archiver.sh git@github.com:erikeldridge/CivicDB.git';
  exit 1;
fi

# Extract project name from repo url
project_name=$( ruby -e "print '$1'.scan(/(\w+)\.git/)[0][0]" );

echo 'Cloning project';
git clone $1 ../$project_name;

echo 'Deleting merge_branch';
git branch -D merge_branch;
git reset --hard ;

echo 'Creating merge_branch';
git co -b merge_branch;

echo 'Defining remote pointing at local copy of project';
git remote rm merge_remote;
git remote add merge_remote ../$project_name;

echo 'Merging in project';
git pull merge_remote master;

echo 'Making directory to house project files';
mkdir $project_name;

echo 'Identifying new files:';
file_names=`git diff master --name-only`
echo $file_names;

echo 'Moving files into project dir'
for f in $file_names
do 

  # Skip this file if it's in the list
  if [ $f = `basename $0` ]
  then
    continue
  fi

  # If the file is nested, only move the parent dir ...
  dir_name=`dirname $f`
  if [ $dir_name = '.' ]
  then
    mv "$f" "$project_name"

  # ... unless the parent dir is already defined.
  elif [ ! -d $project_name/$dir_name ]
  then
    mv "$dir_name" "$project_name";
  fi

done

echo 'Committing changes to merge_branch' 
git add $project_name
git ci -am "Create $project_name dir & add files"

echo 'Merging merge_branch into master'
git co master
git merge merge_branch

echo 'Pushing to github'
git push github master
