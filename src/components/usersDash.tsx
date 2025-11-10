import { useQuery } from '@tanstack/react-query'
import { convexQuery } from '@convex-dev/react-query'
import { api } from "../../convex/_generated/api";
import { CirclePlus, CircleUser, EllipsisVertical } from 'lucide-react';
import { Popover } from 'flowbite-react';
import AddUserForm from './addUserForm';

export default function UsersDash() {
     const {data:users, isLoading:usersLoading}=useQuery(convexQuery(api.users.getAllUsers, {}));
    

    return (
        <div className='mt-10'>
            <div>
                <Popover
                    trigger='click'
                    aria-labelledby="default-popover"
                    className='bottom-0 bg-white h-fit w-auto border border-gray-200 focus:ring-0 focus:outline-0 rounded-md'
                    content= {
                       <AddUserForm />
                    }
                >
                    <button className='flex items-center gap-1 border text-gray-700 p-2 rounded-md border-gray-300'>
                        Add a user
                        <CirclePlus size={20} className=' '/>
                    </button>
                </Popover>
              
            </div>

            <div className='mt-10'>
            {usersLoading ? (
              <p>Loading...</p>
            ) : (
              <ul className='w-full'>
                {users?
                users.map((user) => (
                  <li 
                  className='flex justify-between w-full items-center mb-4'
                  key={user._id}>
                    <div className='flex flex-1 items-center gap-10'>
                         <CircleUser size={30} />
                        <div className=''>
                            <p>{user.fullName} </p>
                        <p>{user.email}</p>
                     
                    </div>
                    </div>
                    <p className='text-gray-700 flex-1 text-sm'>{user.role === 'admin' ? 'Admin' : 'User'}</p>
                    <p className='flex-1'>{new Date(user._creationTime).toLocaleDateString()}</p>

                    <Popover
                    trigger='hover'
                    aria-labelledby="default-popover"
                    className='bottom-0 bg-white flex-1 h-fit w-auto border border-gray-200 focus:ring-0 focus:outline-0 rounded-md'
                    content= {
                        <div className='grid p-2 gap-3'>
                            <button className='p-2 hover:bg-gray-100 w-full text-left rounded-md'>Make Admin</button>
                            <button className='p-2 hover:bg-gray-100 w-full text-left rounded-md text-red-500'>Delete User</button>
                            <button className='p-2 hover:bg-gray-100 w-full text-left rounded-md'>Reset Password</button>
                        </div>
                    }
                    >
                        <button>
                            <EllipsisVertical size={20} className=''/>
                        </button>
                    </Popover>
                </li>
                )) : null}
              </ul>
            )}
            </div>
        </div>
      )
}