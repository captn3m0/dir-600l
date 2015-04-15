require 'find'
require 'pathname'
require 'fileutils'

def point(target, path, root)
  # Create the directories unless it already exists
  relative_target = Pathname.new(target).relative_path_from(Pathname.new path).to_s
  puts "#{path} -> #{relative_target}"
  `rm -f #{path}`
  `ln -s "#{relative_target}" "#{path}"`
end

def process(path, root)
  if File.symlink? path
    #puts "[=] #{path}"
    target = File.readlink(path)
    path = File.expand_path "#{root}/#{path}"
    if (Pathname.new target).absolute?
      newtarget = "#{root}#{target}"
      point(newtarget, path, root)
    else
      #puts "#{path} => #{target}"
    end
  end
end

versions = ['2.04', '2.05', '2.17']
versions.each do |version|
  Dir.chdir "#{__dir__}/#{version}/rootfs/"
  Find.find('.') do |path|
    process(path, "#{__dir__}/#{version}/rootfs")
  end
end