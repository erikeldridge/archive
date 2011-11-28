# This script accepts a github repo url, clones it, and merges it into the archive repository

# Require project repo url as 1st arg
if [ ! $1 ]; then
  echo "$0: repository url required, e.g., $ bash archiver.sh git@github.com:erikeldridge/CivicDB.git"
  exit 1
fi

# Extract repository name from url
repository_name=$( ruby -e "print '$1'.scan(/([\w-]+)\.git/)[0][0]" );

echo "$0: Cloning repository";
git clone $1 ../$repository_name;

echo "$0: Changing directory to ../$repository_name";
cd ../$repository_name

echo "$0: Making archive_branch";
git co -b archive_branch

archive_dir="repository_archive"
mkdir $archive_dir;
echo "$0: Making '$archive_dir' directory to house repository files";

echo "$0: Moving files into archive directory";
for f in *
do
  
  # Skip archive dir itself
  if [ $f = $archive_dir ]
  then
    continue
  fi

  mv "$f" $archive_dir
done

echo "$0: Committing archive directory";
git add $archive_dir
git ci -am "Create $archive_dir dir & add files"

echo "$0: Changing directory back to archive repository";
cd -

echo "$0: Defining remote repository pointing at ../$repository_name";
git remote add merge_remote ../$repository_name;

echo "$0: Merging in repository";
git pull merge_remote archive_branch;

echo "$0: Renaming archive directory to $repository_name"
if [ -d $repository_name ]
  then
  echo "$0: Error: directory with this name already exists"
  exit 1
fi
mv $archive_dir $repository_name

echo "$0: Committing changes to merge_branch"
git add $repository_name
git ci -am "Renaming archive directory to $repository_name"

echo "$0: Deleting remote definition";
git remote rm merge_remote;

echo "$0: Deleting archive branch"
cd -
git co master

# Force deletion because branch is not merged into master
git branch -D archive_branch

cd -
