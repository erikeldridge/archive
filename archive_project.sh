# This script accepts a github repo url, clones it, and merges it into the archive project

# Require project repo url as 1st arg
if [ ! $1 ]; then
  echo 'project url required, e.g., $ bash archiver.sh git@github.com:erikeldridge/CivicDB.git';
  exit 1;
fi

# Extract project name from repo url
project_name=$( ruby -e "print '$1'.scan(/(\w+)\.git/)[0][0]" );

echo "$0: Cloning project";
git clone $1 ../$project_name;

echo "$0: Changing directory to ../$project_name";
cd ../$project_name

archive_dir="${project_name}_archive"
mkdir $archive_dir;
echo "$0: Making '$archive_dir' directory to house project files";

echo "$0: Moving files into archive directory";
for f in *
do
  if [ $f = $archive_dir ]
  then
    continue
  fi

  mv "$f" $archive_dir
done

echo "$0: Committing archive directory";
git add $archive_dir
git ci -am "Create $archive_dir dir & add files"

echo "$0: Changing directory back to archive project";
cd -

echo "$0: Creating merge_branch";
git co -b merge_branch;

echo "$0: Defining remote pointing at local copy of project";
git remote rm merge_remote;
git remote add merge_remote ../$project_name;

echo "$0: Merging in project";
git pull merge_remote master;

echo "$0: Removing '_archive' from archive directory name"
if [ -d $project_name ]
  then
  echo "$0: Error: directory with this name already exists"
  exit 1
fi
mv $archive_dir $project_name

echo "$0: Committing changes to merge_branch"
git add $project_name
git ci -am "Remove '_archive' from archive directory name"

echo "$0: Merging merge_branch into master"
git co master
git merge merge_branch

echo "$0: Deleting merge_branch";
git branch -d merge_branch;
